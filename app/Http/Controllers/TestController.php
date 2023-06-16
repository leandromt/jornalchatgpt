<?php

namespace App\Http\Controllers;

use App\Services\ChatGPT\Actions;
use App\Models\Post;
use App\Models\Suggestion;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestController extends Controller
{
    public function index()
    {
        $dt = Carbon::create(now());
        $day = $dt->format('j \d\e F');

        $promp = [
            'Não dê explicações sobre você. Não peça desculpas. Não fale sobre você. Aja como um historiador. Pesquise sobre acontecimentos e fatos que ocorrecem no dia ' . $day . ' no brasil. Escreva uma matéria completa detalhando os 10 acontecimentos mais relevantes que ocorreram nessa data informada. Retorne dentro de uma tag article html. Destaque com a tag strong as palavras mais relevantes. Adicione links das fontes de pesquisa.',
            'Remova o html do texto. Me fale somente o título da matéria.',
            'Cria um resumo sucinto e objetivo em uma linha.',
            'Pesquise sobre SEO e me informe 5 palavras chaves principais do texto separados por virgula. Exiba somente as palavras chaves. Remova o texto: palavras-chaves',
            'Sugira 5 assuntos relacionados a matéria que você criou. Apenas títulos. Separe por virgula.'
        ];

        $p = new Post();

        foreach ($promp as $k => $msg) {

            $msgs[] = ["role" => "user", "content" => $msg];

            $data = array(
                "model" => "gpt-3.5-turbo",
                "messages" => $msgs
            );

            $act = ((new Actions())->getChatCompletations($data));

            if (isset($act->choices[0])) {
                $msgs[] = ["role" => "assistant", "content" => $act->choices[0]->message->content];
                //dump($act->choices[0]->message->content);
                dump($msgs);

                // Salva o post
                if ($k == 0) {
                    $p->content = $act->choices[0]->message->content;
                }

                if ($k == 1) {
                    $p->title = $act->choices[0]->message->content;
                    $p->slug = Str::slug($act->choices[0]->message->content);
                }

                if ($k == 2) {
                    $p->description = $act->choices[0]->message->content;
                }

                if ($k == 3) {
                    $p->keywords = $act->choices[0]->message->content;
                }

                if ($k == 4) {
                    $array_suggestions = explode(',', $act->choices[0]->message->content);

                    foreach ($array_suggestions as $s) {
                        $suggestion = new Suggestion();
                        $suggestion->name = $this->limpa_texto($s);
                        $suggestion->save();
                    }
                }
            }
        }

        $p->save();

        $url =  'http://127.0.0.1:8000/fatos-historicos/' . $p->slug;
        dump($url);

        dd('fim');
    }

    /**
     * Define o campo slug para o formato slug
     *
     * @param  string  $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        // $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * 
     */
    private function limpa_texto($str)
    {
        $txt = $str;

        if (strlen($str) > 200) {
            $txt = substr($str, 0, 200);
        }

        return str_replace(array("#", "'", ";"), '', $txt);
    }
}
