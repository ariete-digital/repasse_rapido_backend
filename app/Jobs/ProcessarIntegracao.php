<?php

namespace App\Jobs;

use App\Helpers\IntegratorHelper;
use App\Models\IntegracaoLojaConectada;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessarIntegracao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $integracao;

    /**
     * Create a new job instance.
     */
    public function __construct(IntegracaoLojaConectada $integracao)
    {
        $this->integracao = $integracao;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando integração ID: ' . $this->integracao->id);

        $this->integracao->update(['status' => 'em_progresso']);

        try {
            $integratoHelper = new IntegratorHelper();
            $integratoHelper->run();

            $this->integracao->update([
                'status' => 'concluido',
                'resultado' => 'Processo finalizado com sucesso.',
            ]);
            Log::info('Processo concluído ID: ' . $this->integracao->id);
        } catch (\Exception $e) {
            Log::error('Erro ao processar integração ID ' . $this->integracao->id . ': ' . $e->getMessage());
            $this->integracao->update([
                'status' => 'erro',
                'resultado' => $e->getMessage(),
            ]);
        }
    }
}
