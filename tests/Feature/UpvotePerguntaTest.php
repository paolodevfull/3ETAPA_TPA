<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Pergunta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpvotePerguntaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_usuario_anonimo_nao_pode_votar(): void
    {
        $pergunta = $this->criarPergunta('Pergunta protegida');

        $this->post(route('perguntas.votar', $pergunta))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('pergunta_user', 0);
    }

    public function test_usuario_autenticado_pode_adicionar_e_remover_voto(): void
    {
        $usuario = User::factory()->create();
        $pergunta = $this->criarPergunta('Pergunta votável');

        $this->actingAs($usuario)
            ->post(route('perguntas.votar', $pergunta))
            ->assertSessionHas('sucesso');

        $this->assertDatabaseHas('pergunta_user', [
            'pergunta_id' => $pergunta->getKey(),
            'user_id' => $usuario->getKey(),
        ]);

        $this->actingAs($usuario)->post(route('perguntas.votar', $pergunta));

        $this->assertDatabaseMissing('pergunta_user', [
            'pergunta_id' => $pergunta->getKey(),
            'user_id' => $usuario->getKey(),
        ]);
    }

    public function test_perguntas_sao_exibidas_por_quantidade_de_votos(): void
    {
        $evento = Evento::create([
            'titulo' => 'Evento de teste',
            'descricao' => 'Descrição do evento',
        ]);
        $menosVotada = $this->criarPergunta('Pergunta menos votada', $evento);
        $maisVotada = $this->criarPergunta('Pergunta mais votada', $evento);
        $usuarios = User::factory()->count(2)->create();

        $menosVotada->votos()->attach($usuarios->first());
        $maisVotada->votos()->attach($usuarios);

        $this->get(route('eventos.show', $evento))
            ->assertOk()
            ->assertSeeInOrder(['Pergunta mais votada', 'Pergunta menos votada']);
    }

    private function criarPergunta(string $texto, ?Evento $evento = null): Pergunta
    {
        $evento ??= Evento::create([
            'titulo' => 'Evento de teste',
            'descricao' => 'Descrição do evento',
        ]);

        return Pergunta::create([
            'evento_id' => $evento->getKey(),
            'texto' => $texto,
            'status' => 'pendente',
            'is_public' => true,
        ]);
    }
}
