<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\Moderacao;
use App\Models\Pergunta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeracaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_usuario_autenticado_registra_denuncia_validada(): void
    {
        $usuario = User::factory()->create();
        $pergunta = $this->criarPergunta();

        $this->actingAs($usuario)
            ->post(route('perguntas.moderar', $pergunta), ['motivo' => 'Conteúdo fora do tema da palestra.'])
            ->assertSessionHas('sucesso');

        $this->assertDatabaseHas('moderacoes', [
            'pergunta_id' => $pergunta->getKey(),
            'motivo' => 'Conteúdo fora do tema da palestra.',
            'status' => 'analisando',
        ]);
    }

    public function test_motivo_precisa_ter_ao_menos_dez_caracteres(): void
    {
        $usuario = User::factory()->create();
        $pergunta = $this->criarPergunta();

        $this->actingAs($usuario)
            ->from(route('eventos.show', $pergunta->evento))
            ->post(route('perguntas.moderar', $pergunta), ['motivo' => 'curto'])
            ->assertRedirect(route('eventos.show', $pergunta->evento))
            ->assertSessionHasErrors('motivo');
    }

    public function test_apenas_dono_do_evento_decide_status_da_denuncia(): void
    {
        $dono = User::factory()->create();
        $outroUsuario = User::factory()->create();
        $pergunta = $this->criarPergunta($dono);
        $moderacao = Moderacao::create([
            'pergunta_id' => $pergunta->getKey(),
            'motivo' => 'A pergunta deve ser analisada.',
        ]);

        $this->actingAs($outroUsuario)
            ->patch(route('moderacoes.update-status', $moderacao), ['status' => 'aprovada'])
            ->assertForbidden();

        $this->actingAs($dono)
            ->patch(route('moderacoes.update-status', $moderacao), ['status' => 'aprovada'])
            ->assertSessionHas('sucesso');

        $this->assertDatabaseHas('moderacoes', ['id' => $moderacao->getKey(), 'status' => 'aprovada']);
        $this->assertDatabaseHas('perguntas', ['id' => $pergunta->getKey(), 'is_public' => true]);
    }

    private function criarPergunta(?User $dono = null): Pergunta
    {
        $evento = Evento::create([
            'titulo' => 'Evento de teste',
            'descricao' => 'Descrição do evento',
            'user_id' => $dono?->getKey(),
        ]);

        return Pergunta::create([
            'evento_id' => $evento->getKey(),
            'texto' => 'Pergunta que poderá ser moderada.',
            'status' => 'pendente',
            'is_public' => false,
        ]);
    }
}
