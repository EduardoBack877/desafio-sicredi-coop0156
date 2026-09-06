<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Plataforma de Crédito Cooperativo</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        coop: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        darkBg: '#0b0f19',
                        panelBg: '#131c2e',
                        panelBorder: '#1e2d4a',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0b0f19;
            background-image:
                radial-gradient(
                    at 0% 0%,
                    hsla(142, 70%, 15%, 0.15) 0px,
                    transparent 50%
                ),
                radial-gradient(
                    at 100% 100%,
                    hsla(220, 70%, 15%, 0.15) 0px,
                    transparent 50%
                );
        }

        .glass-panel {
            background: rgba(19, 28, 46, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(30, 45, 74, 0.6);
        }
    </style>
</head>

<body class="text-slate-200 min-h-screen flex flex-col font-sans">

    <!-- Header / Navbar -->
    <header
        class="border-b border-panelBorder/50 py-5 glass-panel sticky top-0 z-50"
    >
        <div
            class="max-w-6xl mx-auto px-4 flex justify-between items-center"
        >
            <div class="flex items-center gap-3">

                <div
                    class="h-10 w-10 rounded-xl bg-gradient-to-tr
                           from-green-500 to-emerald-600
                           flex items-center justify-center
                           shadow-lg shadow-green-500/20"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2
                               3 .895 3 2-1.343 2-3 2m0-8
                               c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8
                               m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1
                               M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>

                <div>
                    <h1
                        class="text-xl font-bold tracking-tight
                               bg-gradient-to-r from-emerald-400
                               to-green-300 bg-clip-text text-transparent"
                    >
                        Coop0156
                    </h1>

                    <p class="text-xs text-slate-400">
                        Desafio Análise de Crédito
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-2.5 py-0.5
                           rounded-full text-xs font-medium
                           bg-emerald-500/10 text-emerald-400
                           border border-emerald-500/20"
                >
                    Ambiente de Testes
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main
        class="flex-grow max-w-6xl mx-auto px-4 py-12 w-full
               grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
    >

        <!-- Formulário de Solicitação -->
        <section
            class="lg:col-span-7 glass-panel rounded-3xl p-8
                   shadow-2xl relative overflow-hidden
                   transition-all duration-300 hover:border-panelBorder"
        >
            <div
                class="absolute top-0 right-0 w-32 h-32
                       bg-emerald-500/5 rounded-full blur-2xl"
            ></div>

            <h2 class="text-2xl font-semibold mb-6 flex items-center gap-2">
                <span
                    class="bg-emerald-500/10 text-emerald-400
                           p-2 rounded-lg text-sm"
                >
                    01
                </span>

                Nova Solicitação de Crédito
            </h2>

            <!-- Mensagem de erro -->
            <div
                id="mensagem-erro"
                class="hidden mb-6 bg-red-500/10
                       border border-red-500/20 rounded-xl p-4"
            >
                <div class="flex items-start gap-3">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-red-400 mt-0.5 flex-shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01M5.07 19h13.86
                               c1.54 0 2.5-1.667 1.73-3L13.73 4
                               c-.77-1.333-2.69-1.333-3.46 0L3.34 16
                               c-.77 1.333.19 3 1.73 3z"
                        />
                    </svg>

                    <div>
                        <p class="text-red-400 text-sm font-semibold">
                            Não foi possível concluir a solicitação
                        </p>

                        <p
                            id="texto-erro"
                            class="text-red-300/80 text-sm mt-1"
                        ></p>
                    </div>
                </div>
            </div>

            <form id="form-analise" class="space-y-6">

                <!-- Nome Completo -->
                <div>
                    <label
                        for="nome"
                        class="block text-sm font-medium
                               text-slate-400 mb-2"
                    >
                        Nome Completo
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        required
                        autocomplete="name"
                        placeholder="Digite o nome completo do proponente"
                        class="w-full bg-slate-950/50 border
                               border-panelBorder rounded-xl px-4 py-3
                               text-slate-100 placeholder-slate-500
                               focus:outline-none focus:ring-2
                               focus:ring-emerald-500
                               focus:border-transparent transition-all"
                    >
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- CPF -->
                    <div>
                        <label
                            for="cpf"
                            class="block text-sm font-medium
                                   text-slate-400 mb-2"
                        >
                            CPF
                        </label>

                        <input
                            type="text"
                            id="cpf"
                            name="cpf"
                            required
                            maxlength="14"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="000.000.000-00"
                            class="w-full bg-slate-950/50 border
                                   border-panelBorder rounded-xl px-4 py-3
                                   text-slate-100 placeholder-slate-500
                                   focus:outline-none focus:ring-2
                                   focus:ring-emerald-500
                                   focus:border-transparent transition-all"
                        >
                    </div>

                    <!-- Renda Mensal -->
                    <div>
                        <label
                            for="renda_mensal"
                            class="block text-sm font-medium
                                   text-slate-400 mb-2"
                        >
                            Renda Mensal (R$)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            id="renda_mensal"
                            name="renda_mensal"
                            required
                            placeholder="Ex: 3500.00"
                            class="w-full bg-slate-950/50 border
                                   border-panelBorder rounded-xl px-4 py-3
                                   text-slate-100 placeholder-slate-500
                                   focus:outline-none focus:ring-2
                                   focus:ring-emerald-500
                                   focus:border-transparent transition-all"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Tipo de Crédito -->
                    <div>
                        <label
                            for="tipo_credito"
                            class="block text-sm font-medium
                                   text-slate-400 mb-2"
                        >
                            Tipo de Crédito
                        </label>

                        <select
                            id="tipo_credito"
                            name="tipo_credito"
                            required
                            class="w-full bg-slate-950/50 border
                                   border-panelBorder rounded-xl px-4 py-3
                                   text-slate-300 focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500
                                   focus:border-transparent transition-all"
                        >
                            <option value="" disabled selected>
                                Selecione uma opção
                            </option>

                            <option value="pessoal">
                                Crédito Pessoal
                            </option>

                            <option value="imobiliario">
                                Crédito Imobiliário
                            </option>

                            <option value="automotivo">
                                Crédito Automotivo
                            </option>
                        </select>
                    </div>

                    <!-- Valor Solicitado -->
                    <div>
                        <label
                            for="valor_solicitado"
                            class="block text-sm font-medium
                                   text-slate-400 mb-2"
                        >
                            Valor Requerido (R$)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            id="valor_solicitado"
                            name="valor_solicitado"
                            required
                            placeholder="Ex: 15000.00"
                            class="w-full bg-slate-950/50 border
                                   border-panelBorder rounded-xl px-4 py-3
                                   text-slate-100 placeholder-slate-500
                                   focus:outline-none focus:ring-2
                                   focus:ring-emerald-500
                                   focus:border-transparent transition-all"
                        >
                    </div>
                </div>

                <!-- Botão Enviar -->
                <button
                    type="submit"
                    id="btn-solicitar"
                    class="w-full bg-gradient-to-r
                           from-emerald-500 to-green-600
                           hover:from-emerald-600 hover:to-green-700
                           text-white font-semibold py-4 px-6
                           rounded-xl transition-all duration-200
                           transform active:scale-98
                           shadow-lg shadow-emerald-500/10
                           flex items-center justify-center gap-2"
                >
                    <span id="txt-solicitar">
                        Solicitar Análise de Crédito
                    </span>

                    <svg
                        id="loading-spinner"
                        class="animate-spin h-5 w-5 text-white hidden"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0
                               C5.373 0 0 5.373 0 12h4
                               zm2 5.291A7.962 7.962 0 014 12H0
                               c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                </button>
            </form>
        </section>

        <!-- Resultados -->
        <section class="lg:col-span-5 space-y-6">

            <!-- Resultado vazio -->
            <div
                id="resultado-vazio"
                class="glass-panel rounded-3xl p-8 text-center
                       border-dashed border-2 border-panelBorder
                       flex flex-col items-center justify-center py-20"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-16 w-16 text-slate-600 mb-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7
                           a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                           a1 1 0 01.707.293l5.414 5.414
                           a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>

                <h3 class="text-lg font-medium text-slate-400">
                    Aguardando Solicitação
                </h3>

                <p class="text-sm text-slate-500 mt-2 max-w-xs">
                    Preencha os dados do formulário ao lado e solicite
                    a análise para simular as condições.
                </p>
            </div>

            <!-- Resultado da Análise -->
            <div
                id="resultado-analise"
                class="glass-panel rounded-3xl p-8 shadow-2xl
                       relative overflow-hidden hidden"
            >

                <div
                    id="status-indicator-badge"
                    class="absolute top-6 right-6"
                ></div>

                <h3 class="text-xl font-semibold mb-6 flex items-center gap-2">
                    <span
                        class="bg-emerald-500/10 text-emerald-400
                               p-2 rounded-lg text-sm"
                    >
                        02
                    </span>

                    Resultado da Análise
                </h3>

                <!-- Dados da análise -->
                <div class="space-y-4 divide-y divide-panelBorder">

                    <div class="flex justify-between gap-4 pt-1">
                        <span class="text-slate-400 text-sm">
                            Proponente
                        </span>

                        <span
                            id="res-nome"
                            class="font-medium text-slate-100 text-right"
                        >
                            -
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 pt-4">
                        <span class="text-slate-400 text-sm">
                            CPF
                        </span>

                        <span
                            id="res-cpf"
                            class="font-medium text-slate-100"
                        >
                            -
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 pt-4">
                        <span class="text-slate-400 text-sm">
                            Score de Crédito
                        </span>

                        <span
                            id="res-score"
                            class="font-medium text-slate-100"
                        >
                            -
                        </span>
                    </div>

                    <div class="flex justify-between gap-4 pt-4">
                        <span class="text-slate-400 text-sm">
                            Status da Análise
                        </span>

                        <span
                            id="res-status"
                            class="font-bold"
                        >
                            -
                        </span>
                    </div>

                    <!-- Bloco Aprovado -->
                    <div
                        id="dados-aprovado"
                        class="space-y-4 pt-4 hidden"
                    >
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-400 text-sm">
                                Taxa de Juros Aplicada
                            </span>

                            <span
                                id="res-taxa"
                                class="font-medium text-emerald-400"
                            >
                                -
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-400 text-sm">
                                Parcela Mensal
                            </span>

                            <span
                                id="res-parcela"
                                class="font-bold text-lg text-emerald-400"
                            >
                                -
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-400 text-sm">
                                Renda Comprometida
                            </span>

                            <span
                                id="res-comprometimento"
                                class="font-medium text-slate-100"
                            >
                                -
                            </span>
                        </div>
                    </div>

                    <!-- Bloco Reprovado -->
                    <div
                        id="dados-reprovado"
                        class="pt-4 hidden"
                    >
                        <div
                            class="bg-red-500/10 border border-red-500/20
                                   rounded-xl p-4 mt-2"
                        >
                            <span
                                class="text-red-400 text-xs block
                                       font-semibold uppercase
                                       tracking-wider mb-1"
                            >
                                Motivo da Recusa
                            </span>

                            <p
                                id="res-motivo"
                                class="text-slate-200 text-sm"
                            >
                                -
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ir para simulação -->
                <div
                    id="container-simulacao"
                    class="mt-8 pt-6 border-t border-panelBorder hidden"
                >
                    <a
                        id="btn-simulacao"
                        href="#"
                        class="w-full bg-gradient-to-r
                               from-blue-500 to-indigo-600
                               hover:from-blue-600 hover:to-indigo-700
                               text-white font-semibold py-4 px-6
                               rounded-xl transition-all duration-200
                               transform active:scale-98
                               shadow-lg shadow-indigo-500/10
                               flex items-center justify-center gap-2"
                    >
                        <span>
                            Visualizar Simulação e Continuar
                        </span>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                            />
                        </svg>
                    </a>

                    <p
                        class="text-center text-xs text-slate-500 mt-3"
                    >
                        Confira todas as condições antes de confirmar
                        a contratação do crédito.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer
        class="border-t border-panelBorder/40
               py-6 text-center text-xs text-slate-600"
    >
        <div class="max-w-6xl mx-auto px-4">
            <p>
                &copy; 2026 CoopCred. Todos os direitos reservados.
                Desafio Técnico Laravel.
            </p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /*
             * Elementos principais
             */
            const form = document.getElementById('form-analise');

            const btnSolicitar =
                document.getElementById('btn-solicitar');

            const txtSolicitar =
                document.getElementById('txt-solicitar');

            const loadingSpinner =
                document.getElementById('loading-spinner');

            const resultadoVazio =
                document.getElementById('resultado-vazio');

            const resultadoAnalise =
                document.getElementById('resultado-analise');

            /*
             * Erros
             */
            const mensagemErro =
                document.getElementById('mensagem-erro');

            const textoErro =
                document.getElementById('texto-erro');

            /*
             * Resultado
             */
            const badge =
                document.getElementById('status-indicator-badge');

            const resNome =
                document.getElementById('res-nome');

            const resCpf =
                document.getElementById('res-cpf');

            const resScore =
                document.getElementById('res-score');

            const resStatus =
                document.getElementById('res-status');

            /*
             * Resultado aprovado
             */
            const dadosAprovado =
                document.getElementById('dados-aprovado');

            const resTaxa =
                document.getElementById('res-taxa');

            const resParcela =
                document.getElementById('res-parcela');

            const resComprometimento =
                document.getElementById('res-comprometimento');

            /*
             * Resultado reprovado
             */
            const dadosReprovado =
                document.getElementById('dados-reprovado');

            const resMotivo =
                document.getElementById('res-motivo');

            /*
             * Simulação
             */
            const containerSimulacao =
                document.getElementById('container-simulacao');

            const btnSimulacao =
                document.getElementById('btn-simulacao');

            /*
             * CPF
             */
            const inputCpf =
                document.getElementById('cpf');


            /**
             * Formata valores monetários.
             */
            function formatarMoeda(valor) {
                return Number(valor).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
            }


            /**
             * Formata CPF para exibição.
             */
            function formatarCpf(cpf) {
                const numeros =
                    String(cpf).replace(/\D/g, '');

                if (numeros.length !== 11) {
                    return cpf;
                }

                return numeros.replace(
                    /(\d{3})(\d{3})(\d{3})(\d{2})/,
                    '$1.$2.$3-$4'
                );
            }


            /**
             * Máscara visual de CPF no formulário.
             */
            function aplicarMascaraCpf(valor) {
                let numeros =
                    valor.replace(/\D/g, '').slice(0, 11);

                numeros = numeros.replace(
                    /^(\d{3})(\d)/,
                    '$1.$2'
                );

                numeros = numeros.replace(
                    /^(\d{3})\.(\d{3})(\d)/,
                    '$1.$2.$3'
                );

                numeros = numeros.replace(
                    /\.(\d{3})(\d)/,
                    '.$1-$2'
                );

                return numeros;
            }


            inputCpf.addEventListener('input', () => {
                inputCpf.value =
                    aplicarMascaraCpf(inputCpf.value);
            });


            /**
             * Mostra erro na tela.
             */
            function exibirErro(mensagem) {
                textoErro.textContent = mensagem;
                mensagemErro.classList.remove('hidden');

                mensagemErro.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }


            /**
             * Remove erro anterior.
             */
            function limparErro() {
                textoErro.textContent = '';
                mensagemErro.classList.add('hidden');
            }


            /**
             * Estado de carregamento.
             */
            function setLoading(loading) {
                btnSolicitar.disabled = loading;

                if (loading) {
                    btnSolicitar.classList.add(
                        'opacity-70',
                        'cursor-not-allowed'
                    );

                    txtSolicitar.textContent =
                        'Processando análise...';

                    loadingSpinner.classList.remove('hidden');
                } else {
                    btnSolicitar.classList.remove(
                        'opacity-70',
                        'cursor-not-allowed'
                    );

                    txtSolicitar.textContent =
                        'Solicitar Análise de Crédito';

                    loadingSpinner.classList.add('hidden');
                }
            }


            /**
             * Limpa os blocos de resultado.
             */
            function limparResultado() {
                dadosAprovado.classList.add('hidden');
                dadosReprovado.classList.add('hidden');
                containerSimulacao.classList.add('hidden');

                badge.innerHTML = '';
            }


            /**
             * Exibe análise aprovada.
             */
            function exibirAprovado(analise) {
                resStatus.textContent = 'APROVADO';

                resStatus.className =
                    'font-bold text-emerald-400';

                badge.innerHTML = `
                    <span
                        class="inline-flex items-center
                               px-3 py-1 rounded-full text-xs
                               font-semibold bg-emerald-500/10
                               text-emerald-400
                               border border-emerald-500/20"
                    >
                        APROVADO
                    </span>
                `;

                resTaxa.textContent =
                    `${Number(analise.taxa_juros)
                        .toLocaleString('pt-BR')}% ao mês`;

                resParcela.textContent =
                    `12x de ${formatarMoeda(
                        analise.valor_parcela
                    )}`;

                const renda =
                    Number(analise.renda_mensal);

                const parcela =
                    Number(analise.valor_parcela);

                const percentualComprometimento =
                    renda > 0
                        ? (parcela / renda) * 100
                        : 0;

                resComprometimento.textContent =
                    `${percentualComprometimento
                        .toFixed(2)
                        .replace('.', ',')}%`;

                dadosAprovado.classList.remove('hidden');

                btnSimulacao.href =
                    `/simulacao/${analise.id}`;

                containerSimulacao.classList.remove('hidden');
            }


            /**
             * Exibe análise reprovada.
             */
            function exibirReprovado(analise) {
                resStatus.textContent = 'REPROVADO';

                resStatus.className =
                    'font-bold text-red-400';

                badge.innerHTML = `
                    <span
                        class="inline-flex items-center
                               px-3 py-1 rounded-full text-xs
                               font-semibold bg-red-500/10
                               text-red-400
                               border border-red-500/20"
                    >
                        REPROVADO
                    </span>
                `;

                resMotivo.textContent =
                    analise.motivo_rejeicao
                    ?? 'A solicitação não atende aos critérios de crédito.';

                dadosReprovado.classList.remove('hidden');
            }


            /**
             * Exibe o resultado retornado pela API.
             */
            function exibirResultado(analise) {
                resultadoVazio.classList.add('hidden');
                resultadoAnalise.classList.remove('hidden');

                limparResultado();

                resNome.textContent =
                    analise.nome ?? '-';

                resCpf.textContent =
                    formatarCpf(analise.cpf ?? '');

                resScore.textContent =
                    analise.score ?? '-';

                if (analise.status === 'aprovado') {
                    exibirAprovado(analise);
                    return;
                }

                if (analise.status === 'reprovado') {
                    exibirReprovado(analise);
                    return;
                }

                resStatus.textContent =
                    String(analise.status ?? 'PENDENTE')
                        .toUpperCase();

                resStatus.className =
                    'font-bold text-amber-400';

                badge.innerHTML = `
                    <span
                        class="inline-flex items-center
                               px-3 py-1 rounded-full text-xs
                               font-semibold bg-amber-500/10
                               text-amber-400
                               border border-amber-500/20"
                    >
                        PENDENTE
                    </span>
                `;
            }


            /**
             * Obtém mensagem de erro do Laravel.
             */
            function obterMensagemErro(data, status) {
                /*
                 * Erros de validação do Form Request.
                 */
                if (
                    status === 422 &&
                    data &&
                    data.errors
                ) {
                    const erros =
                        Object.values(data.errors);

                    if (
                        erros.length > 0 &&
                        Array.isArray(erros[0]) &&
                        erros[0].length > 0
                    ) {
                        return erros[0][0];
                    }
                }

                /*
                 * Erros tratados no Controller.
                 */
                if (
                    data &&
                    typeof data.message === 'string'
                ) {
                    return data.message;
                }

                if (status === 502) {
                    return 'O serviço de consulta de crédito está temporariamente indisponível.';
                }

                if (status === 504) {
                    return 'A consulta ao Bureau demorou mais que o esperado. Tente novamente.';
                }

                return 'Não foi possível realizar a análise de crédito.';
            }


            /**
             * Envio do formulário.
             */
            form.addEventListener(
                'submit',
                async (event) => {

                    event.preventDefault();

                    limparErro();
                    limparResultado();
                    setLoading(true);

                    /*
                     * O backend exige CPF com 11 dígitos.
                     * A máscara visual é removida antes do envio.
                     */
                    const cpf =
                        inputCpf.value.replace(/\D/g, '');

                    const payload = {
                        nome:
                            document
                                .getElementById('nome')
                                .value
                                .trim(),

                        cpf: cpf,

                        renda_mensal:
                            Number(
                                document
                                    .getElementById('renda_mensal')
                                    .value
                            ),

                        tipo_credito:
                            document
                                .getElementById('tipo_credito')
                                .value,

                        valor_solicitado:
                            Number(
                                document
                                    .getElementById('valor_solicitado')
                                    .value
                            )
                    };

                    try {
                        const response =
                            await fetch(
                                '/api/analise-credito',
                                {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type':
                                            'application/json',

                                        'Accept':
                                            'application/json'
                                    },

                                    body:
                                        JSON.stringify(payload)
                                }
                            );

                        let data;

                        try {
                            data = await response.json();
                        } catch (erroJson) {
                            throw new Error(
                                'O servidor retornou uma resposta inválida.'
                            );
                        }

                        if (!response.ok) {
                            throw new Error(
                                obterMensagemErro(
                                    data,
                                    response.status
                                )
                            );
                        }

                        exibirResultado(data);

                        resultadoAnalise.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                    } catch (erro) {
                        console.error(
                            'Erro ao solicitar análise:',
                            erro
                        );

                        exibirErro(
                            erro.message ||
                            'Não foi possível comunicar com o servidor.'
                        );

                    } finally {
                        setLoading(false);
                    }
                }
            );
        });
    </script>

</body>
</html>