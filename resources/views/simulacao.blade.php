<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Simulação de Crédito — Coop0156</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif']
                    },
                    colors: {
                        darkBg: '#0b0f19',
                        panelBg: '#131c2e',
                        panelBorder: '#1e2d4a'
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
                    at 20% 20%,
                    hsla(210, 70%, 15%, 0.20) 0px,
                    transparent 50%
                ),
                radial-gradient(
                    at 80% 80%,
                    hsla(142, 70%, 12%, 0.15) 0px,
                    transparent 50%
                );
        }

        .glass-panel {
            background: rgba(19, 28, 46, 0.70);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(30, 45, 74, 0.60);
        }

        .glass-panel-strong {
            background: rgba(19, 28, 46, 0.90);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(30, 45, 74, 0.80);
        }

        button:disabled {
            cursor: not-allowed;
        }
    </style>
</head>

@php
    /*
     * Normalizamos alguns valores para que a View não dependa
     * diretamente de detalhes de apresentação dos Enums.
     */
    $statusAnalise = $analise->status instanceof \BackedEnum
        ? $analise->status->value
        : $analise->status;

    $tipoCredito = $analise->tipo_credito instanceof \BackedEnum
        ? $analise->tipo_credito->value
        : $analise->tipo_credito;

    $tiposCredito = [
        'pessoal' => 'Crédito Pessoal',
        'imobiliario' => 'Crédito Imobiliário',
        'automotivo' => 'Crédito Automotivo',
    ];

    $tipoCreditoFormatado = $tiposCredito[$tipoCredito]
        ?? ucfirst((string) $tipoCredito);

    /*
     * Formatação visual do CPF.
     */
    $cpfNumeros = preg_replace('/\D/', '', $analise->cpf);

    $cpfFormatado = strlen($cpfNumeros) === 11
        ? preg_replace(
            '/(\d{3})(\d{3})(\d{3})(\d{2})/',
            '$1.$2.$3-$4',
            $cpfNumeros
        )
        : $analise->cpf;

    /*
     * Cálculos utilizados somente para apresentação.
     *
     * A aprovação e as regras de negócio continuam sendo
     * responsabilidade do backend.
     */
    $valorSolicitado = (float) $analise->valor_solicitado;
    $taxaJuros = (float) $analise->taxa_juros;
    $valorParcela = (float) $analise->valor_parcela;
    $rendaMensal = (float) $analise->renda_mensal;

    $quantidadeParcelas = 12;

    $jurosTotais = $valorSolicitado
        * ($taxaJuros / 100)
        * $quantidadeParcelas;

    $valorTotal = $valorSolicitado + $jurosTotais;

    $comprometimento = $rendaMensal > 0
        ? ($valorParcela / $rendaMensal) * 100
        : 0;

    $aprovado = $statusAnalise === 'aprovado';
    $contratado = $statusAnalise === 'contratado';
@endphp

<body class="text-slate-200 min-h-screen flex flex-col font-sans">

    <!-- ============================================================= -->
    <!-- HEADER                                                        -->
    <!-- ============================================================= -->

    <header
        class="border-b border-panelBorder/50 py-5
               glass-panel sticky top-0 z-40"
    >
        <div
            class="max-w-5xl mx-auto px-4
                   flex justify-between items-center"
        >

            <a
                href="/"
                class="flex items-center gap-3 group"
            >
                <div
                    class="h-10 w-10 rounded-xl
                           bg-gradient-to-tr
                           from-green-500 to-emerald-600
                           flex items-center justify-center
                           shadow-lg shadow-green-500/20
                           group-hover:scale-105
                           transition-transform"
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
                               c1.11 0 2.08.402 2.599 1
                               M12 8V7m0 1v8m0 0v1
                               m0-1c-1.11 0-2.08-.402-2.599-1
                               M21 12a9 9 0 11-18 0
                               9 9 0 0118 0z"
                        />
                    </svg>
                </div>

                <div>
                    <h1
                        class="text-xl font-bold tracking-tight
                               bg-gradient-to-r
                               from-emerald-400 to-green-300
                               bg-clip-text text-transparent"
                    >
                        Coop0156
                    </h1>

                    <p class="text-xs text-slate-400">
                        Desafio Análise de Crédito
                    </p>
                </div>
            </a>

            <a
                href="/"
                class="text-sm text-slate-400
                       hover:text-emerald-400
                       transition-colors
                       flex items-center gap-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 19l-7-7m0 0
                           l7-7m-7 7h18"
                    />
                </svg>

                Nova Análise
            </a>

        </div>
    </header>


    <!-- ============================================================= -->
    <!-- CONTEÚDO PRINCIPAL                                            -->
    <!-- ============================================================= -->

    <main
        class="flex-grow max-w-5xl
               mx-auto px-4 py-12 w-full"
    >

        <!-- Breadcrumb -->

        <nav
            class="flex items-center gap-2
                   text-sm text-slate-500 mb-8"
        >
            <a
                href="/"
                class="hover:text-slate-300
                       transition-colors"
            >
                Análise
            </a>

            <span>/</span>

            <span class="text-slate-300">
                Simulação #{{ $analise->id }}
            </span>
        </nav>


        <!-- ========================================================= -->
        <!-- CABEÇALHO DA SIMULAÇÃO                                    -->
        <!-- ========================================================= -->

        <div
            class="flex flex-col sm:flex-row
                   items-start sm:items-center
                   justify-between gap-4 mb-8"
        >

            <div>
                <h2
                    class="text-3xl font-bold text-white"
                >
                    Simulação de Crédito
                </h2>

                <p
                    class="text-slate-400 mt-1"
                >
                    Revise todas as condições antes de
                    confirmar a contratação.
                </p>
            </div>


            @if($aprovado)

                <span
                    class="inline-flex items-center gap-2
                           px-4 py-2 rounded-full
                           text-sm font-semibold
                           bg-emerald-500/10
                           text-emerald-400
                           border border-emerald-500/20"
                >
                    <span
                        class="h-2 w-2 rounded-full
                               bg-emerald-400 animate-pulse"
                    ></span>

                    Crédito Aprovado
                </span>

            @elseif($contratado)

                <span
                    class="inline-flex items-center gap-2
                           px-4 py-2 rounded-full
                           text-sm font-semibold
                           bg-blue-500/10
                           text-blue-400
                           border border-blue-500/20"
                >
                    <span
                        class="h-2 w-2 rounded-full
                               bg-blue-400"
                    ></span>

                    Crédito Contratado
                </span>

            @else

                <span
                    class="inline-flex items-center gap-2
                           px-4 py-2 rounded-full
                           text-sm font-semibold
                           bg-amber-500/10
                           text-amber-400
                           border border-amber-500/20"
                >
                    Status:
                    {{ strtoupper((string) $statusAnalise) }}
                </span>

            @endif

        </div>


        <!-- ========================================================= -->
        <!-- CARDS PRINCIPAIS                                          -->
        <!-- ========================================================= -->

        <div
            class="grid grid-cols-1
                   lg:grid-cols-3 gap-6"
        >

            <!-- ===================================================== -->
            <!-- PROPONENTE                                            -->
            <!-- ===================================================== -->

            <div
                class="glass-panel rounded-2xl p-6
                       transition-all duration-200
                       hover:border-slate-600"
            >

                <h3
                    class="text-xs font-semibold
                           uppercase tracking-widest
                           text-slate-500 mb-5"
                >
                    Proponente
                </h3>


                <div class="space-y-4">

                    <div>
                        <p
                            class="text-xs text-slate-500"
                        >
                            Nome
                        </p>

                        <p
                            class="font-semibold
                                   text-slate-100 mt-1"
                        >
                            {{ $analise->nome }}
                        </p>
                    </div>


                    <div>
                        <p
                            class="text-xs text-slate-500"
                        >
                            CPF
                        </p>

                        <p
                            class="font-medium
                                   text-slate-200
                                   font-mono mt-1"
                        >
                            {{ $cpfFormatado }}
                        </p>
                    </div>


                    <div>
                        <p
                            class="text-xs text-slate-500"
                        >
                            Renda Mensal
                        </p>

                        <p
                            class="font-medium
                                   text-slate-200 mt-1"
                        >
                            R$
                            {{ number_format(
                                $rendaMensal,
                                2,
                                ',',
                                '.'
                            ) }}
                        </p>
                    </div>


                    <div>
                        <p
                            class="text-xs text-slate-500"
                        >
                            Tipo de Crédito
                        </p>

                        <p
                            class="font-medium
                                   text-slate-200 mt-1"
                        >
                            {{ $tipoCreditoFormatado }}
                        </p>
                    </div>

                </div>
            </div>


            <!-- ===================================================== -->
            <!-- SCORE                                                 -->
            <!-- ===================================================== -->

            <div
                class="glass-panel rounded-2xl p-6
                       transition-all duration-200
                       hover:border-emerald-500/30"
            >

                <h3
                    class="text-xs font-semibold
                           uppercase tracking-widest
                           text-slate-500 mb-4"
                >
                    Score de Crédito
                </h3>


                <div
                    class="flex flex-col
                           items-center justify-center
                           h-36"
                >

                    <p
                        class="text-6xl font-bold
                               bg-gradient-to-b
                               from-emerald-300
                               to-emerald-500
                               bg-clip-text
                               text-transparent"
                    >
                        {{ $analise->score }}
                    </p>

                    <p
                        class="text-slate-400
                               text-sm mt-2"
                    >
                        Pontuação Obtida
                    </p>

                </div>


                <div
                    class="mt-4 pt-4
                           border-t border-panelBorder"
                >

                    <p
                        class="text-xs text-slate-500"
                    >
                        Taxa de Juros Aplicada
                    </p>

                    <div
                        class="flex items-end
                               justify-between mt-1"
                    >
                        <p
                            class="text-2xl font-bold
                                   text-emerald-400"
                        >
                            {{ number_format(
                                $taxaJuros,
                                1,
                                ',',
                                '.'
                            ) }}%
                        </p>

                        <p
                            class="text-xs
                                   text-slate-500 mb-1"
                        >
                            ao mês
                        </p>
                    </div>

                </div>
            </div>


            <!-- ===================================================== -->
            <!-- CONDIÇÕES FINANCEIRAS                                 -->
            <!-- ===================================================== -->

            <div
                class="glass-panel rounded-2xl p-6
                       transition-all duration-200
                       hover:border-blue-500/30"
            >

                <h3
                    class="text-xs font-semibold
                           uppercase tracking-widest
                           text-slate-500 mb-5"
                >
                    Condições
                </h3>


                <div class="space-y-4">

                    <div>
                        <p
                            class="text-xs text-slate-500"
                        >
                            Valor Solicitado
                        </p>

                        <p
                            class="font-semibold
                                   text-slate-100
                                   text-lg mt-1"
                        >
                            R$
                            {{ number_format(
                                $valorSolicitado,
                                2,
                                ',',
                                '.'
                            ) }}
                        </p>
                    </div>


                    <div
                        class="grid grid-cols-2 gap-4"
                    >

                        <div>
                            <p
                                class="text-xs text-slate-500"
                            >
                                Parcelas
                            </p>

                            <p
                                class="font-semibold
                                       text-slate-200 mt-1"
                            >
                                12x fixas
                            </p>
                        </div>


                        <div>
                            <p
                                class="text-xs text-slate-500"
                            >
                                Juros Totais
                            </p>

                            <p
                                class="font-semibold
                                       text-slate-200 mt-1"
                            >
                                R$
                                {{ number_format(
                                    $jurosTotais,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                            </p>
                        </div>

                    </div>


                    <div
                        class="pt-4
                               border-t border-panelBorder"
                    >
                        <p
                            class="text-xs text-slate-500"
                        >
                            Valor da Parcela
                        </p>

                        <p
                            class="text-2xl font-bold
                                   text-white mt-1"
                        >
                            R$
                            {{ number_format(
                                $valorParcela,
                                2,
                                ',',
                                '.'
                            ) }}
                        </p>
                    </div>

                </div>
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- RESUMO FINANCEIRO                                         -->
        <!-- ========================================================= -->

        <div
            class="glass-panel rounded-2xl
                   p-6 mt-6"
        >

            <div
                class="flex items-center
                       justify-between gap-4 mb-5"
            >

                <div>
                    <h3
                        class="font-semibold
                               text-slate-100"
                    >
                        Resumo da Operação
                    </h3>

                    <p
                        class="text-xs
                               text-slate-500 mt-1"
                    >
                        Valores estimados com juros simples
                        durante 12 meses.
                    </p>
                </div>


                <div
                    class="h-10 w-10
                           rounded-xl
                           bg-emerald-500/10
                           flex items-center
                           justify-center
                           flex-shrink-0"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-emerald-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2
                               s1.343 2 3 2 3 .895 3 2
                               -1.343 2-3 2m0-8
                               c1.11 0 2.08.402 2.599 1
                               M12 8V7m0 1v8m0 0v1
                               m0-1c-1.11 0-2.08-.402-2.599-1
                               M21 12a9 9 0 11-18 0
                               9 9 0 0118 0z"
                        />
                    </svg>
                </div>

            </div>


            <div
                class="grid grid-cols-1
                       sm:grid-cols-3 gap-4"
            >

                <div
                    class="rounded-xl
                           bg-slate-950/30
                           border border-panelBorder
                           p-4"
                >
                    <p
                        class="text-xs text-slate-500"
                    >
                        Principal
                    </p>

                    <p
                        class="text-lg font-semibold
                               text-slate-200 mt-1"
                    >
                        R$
                        {{ number_format(
                            $valorSolicitado,
                            2,
                            ',',
                            '.'
                        ) }}
                    </p>
                </div>


                <div
                    class="rounded-xl
                           bg-slate-950/30
                           border border-panelBorder
                           p-4"
                >
                    <p
                        class="text-xs text-slate-500"
                    >
                        Juros no Período
                    </p>

                    <p
                        class="text-lg font-semibold
                               text-slate-200 mt-1"
                    >
                        R$
                        {{ number_format(
                            $jurosTotais,
                            2,
                            ',',
                            '.'
                        ) }}
                    </p>
                </div>


                <div
                    class="rounded-xl
                           bg-emerald-500/5
                           border border-emerald-500/20
                           p-4"
                >
                    <p
                        class="text-xs text-emerald-500"
                    >
                        Total Estimado
                    </p>

                    <p
                        class="text-xl font-bold
                               text-emerald-400 mt-1"
                    >
                        R$
                        {{ number_format(
                            $valorTotal,
                            2,
                            ',',
                            '.'
                        ) }}
                    </p>
                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- COMPROMETIMENTO DE RENDA                                  -->
        <!-- ========================================================= -->

        <div
            class="glass-panel rounded-2xl
                   p-5 mt-6
                   flex items-start gap-4"
        >

            <div
                class="h-10 w-10
                       rounded-xl
                       bg-blue-500/10
                       flex items-center
                       justify-center
                       flex-shrink-0"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-blue-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1
                           m1-4h.01
                           M21 12a9 9 0 11-18 0
                           9 9 0 0118 0z"
                    />
                </svg>
            </div>


            <div class="flex-grow">

                <div
                    class="flex flex-col
                           sm:flex-row
                           sm:items-center
                           sm:justify-between
                           gap-2"
                >
                    <p
                        class="text-sm font-medium
                               text-slate-200"
                    >
                        Comprometimento de renda
                    </p>

                    <span
                        class="text-blue-400
                               font-semibold text-sm"
                    >
                        {{ number_format(
                            $comprometimento,
                            2,
                            ',',
                            '.'
                        ) }}%
                    </span>
                </div>


                <p
                    class="text-xs
                           text-slate-400 mt-1"
                >
                    A parcela de
                    <strong class="text-slate-300">
                        R$
                        {{ number_format(
                            $valorParcela,
                            2,
                            ',',
                            '.'
                        ) }}
                    </strong>

                    representa aproximadamente

                    <strong class="text-blue-400">
                        {{ number_format(
                            $comprometimento,
                            2,
                            ',',
                            '.'
                        ) }}%
                    </strong>

                    da renda mensal declarada de

                    <strong class="text-slate-300">
                        R$
                        {{ number_format(
                            $rendaMensal,
                            2,
                            ',',
                            '.'
                        ) }}.
                    </strong>
                </p>


                <div
                    class="w-full h-2
                           bg-slate-800
                           rounded-full
                           overflow-hidden mt-4"
                >
                    <div
                        class="h-full
                               bg-gradient-to-r
                               from-blue-500
                               to-emerald-500
                               rounded-full"
                        style="width: {{ min($comprometimento, 100) }}%"
                    ></div>
                </div>


                <div
                    class="flex justify-between
                           text-[10px]
                           text-slate-600 mt-1"
                >
                    <span>0%</span>
                    <span>Limite: 30%</span>
                    <span>100%</span>
                </div>

            </div>
        </div>


        <!-- ========================================================= -->
        <!-- CONTRATAÇÃO                                               -->
        <!-- ========================================================= -->

        <div
            class="mt-8
                   glass-panel rounded-2xl
                   p-8 text-center"
        >

            <!-- Erro dinâmico da API -->

            <div
                id="erro-contratacao"
                role="alert"
                aria-live="assertive"
                class="hidden
                       bg-red-500/10
                       border border-red-500/20
                       rounded-xl
                       p-4 mb-6
                       text-left"
            >
                <div
                    class="flex items-start gap-3"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5
                               text-red-400
                               flex-shrink-0 mt-0.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01
                               M5.07 19h13.86
                               c1.54 0 2.5-1.667 1.73-3
                               L13.73 4
                               c-.77-1.333-2.69-1.333-3.46 0
                               L3.34 16
                               c-.77 1.333.19 3 1.73 3z"
                        />
                    </svg>


                    <div>
                        <p
                            class="text-red-400
                                   text-sm font-semibold"
                        >
                            Não foi possível concluir a contratação
                        </p>

                        <p
                            id="texto-erro-contratacao"
                            class="text-red-300/80
                                   text-sm mt-1"
                        ></p>
                    </div>

                </div>
            </div>


            @if(session('erro'))

                <div
                    class="bg-red-500/10
                           border border-red-500/20
                           rounded-xl
                           p-4 mb-6
                           text-red-400 text-sm"
                >
                    {{ session('erro') }}
                </div>

            @endif


            @if($aprovado)

                <div
                    class="h-14 w-14
                           rounded-2xl
                           bg-indigo-500/10
                           flex items-center
                           justify-center
                           mx-auto mb-5"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-indigo-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4
                               m5.618-4.016
                               A11.955 11.955 0 0112 2.944
                               a11.955 11.955 0 01-8.618 3.04
                               A12.02 12.02 0 003 9
                               c0 5.591 3.824 10.29 9 11.622
                               5.176-1.332 9-6.03
                               9-11.622
                               0-1.042-.133-2.052-.382-3.016z"
                        />
                    </svg>
                </div>


                <h3
                    class="text-xl
                           font-semibold
                           text-white mb-2"
                >
                    Confirmar Contratação
                </h3>


                <p
                    class="text-slate-400
                           text-sm mb-8
                           max-w-lg mx-auto"
                >
                    Revise as condições acima.
                    Ao confirmar, o crédito aprovado será
                    formalmente marcado como contratado.
                </p>


                <div
                    class="flex flex-col
                           sm:flex-row gap-4
                           justify-center"
                >

                    <a
                        href="/"
                        class="px-8 py-3.5
                               rounded-xl
                               border border-panelBorder
                               text-slate-400
                               hover:text-slate-200
                               hover:border-slate-500
                               transition-all
                               font-medium text-sm"
                    >
                        Cancelar
                    </a>


                    <button
                        type="button"
                        id="btn-confirmar"
                        class="px-10 py-3.5
                               bg-gradient-to-r
                               from-blue-500
                               to-indigo-600
                               hover:from-blue-600
                               hover:to-indigo-700
                               text-white
                               font-semibold
                               rounded-xl
                               transition-all
                               duration-200
                               shadow-lg
                               shadow-indigo-500/20
                               flex items-center
                               gap-2 justify-center"
                    >

                        <span
                            id="txt-confirmar"
                        >
                            Confirmar Contratação
                        </span>


                        <svg
                            id="icone-confirmar"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>


                        <svg
                            id="spinner-confirmar"
                            class="animate-spin
                                   h-4 w-4 hidden"
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
                                   zm2 5.291
                                   A7.962 7.962 0 014 12H0
                                   c0 3.042 1.135 5.824
                                   3 7.938l3-2.647z"
                            ></path>
                        </svg>

                    </button>

                </div>


                <p
                    class="text-xs
                           text-slate-600 mt-5"
                >
                    A contratação só é permitida para
                    análises com status aprovado.
                </p>


            @elseif($contratado)

                <div
                    class="h-16 w-16
                           bg-emerald-500/10
                           text-emerald-400
                           rounded-full
                           flex items-center
                           justify-center
                           mx-auto mb-5"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>


                <h3
                    class="text-xl
                           font-bold
                           text-emerald-400"
                >
                    Crédito já contratado
                </h3>


                <p
                    class="text-sm
                           text-slate-400 mt-2"
                >
                    Esta análise já teve sua
                    contratação confirmada.
                </p>


                <a
                    href="/"
                    class="inline-block
                           mt-6 px-8 py-3
                           bg-emerald-500/10
                           border border-emerald-500/20
                           text-emerald-400
                           hover:bg-emerald-500/20
                           rounded-xl
                           text-sm font-medium
                           transition-all"
                >
                    Iniciar Nova Análise
                </a>


            @else

                <div
                    class="h-14 w-14
                           rounded-2xl
                           bg-amber-500/10
                           flex items-center
                           justify-center
                           mx-auto mb-5"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-amber-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01
                               M5.07 19h13.86
                               c1.54 0 2.5-1.667 1.73-3
                               L13.73 4
                               c-.77-1.333-2.69-1.333-3.46 0
                               L3.34 16
                               c-.77 1.333.19 3 1.73 3z"
                        />
                    </svg>
                </div>


                <h3
                    class="text-xl
                           font-semibold
                           text-white mb-2"
                >
                    Contratação indisponível
                </h3>


                <p
                    class="text-slate-400
                           text-sm max-w-lg mx-auto"
                >
                    Esta análise possui status
                    <strong class="text-amber-400">
                        {{ strtoupper((string) $statusAnalise) }}
                    </strong>

                    e não pode ser contratada.
                </p>


                <a
                    href="/"
                    class="inline-block
                           mt-6 px-8 py-3
                           border border-panelBorder
                           text-slate-400
                           hover:text-slate-200
                           hover:border-slate-500
                           rounded-xl
                           text-sm font-medium
                           transition-all"
                >
                    Voltar para Análise
                </a>

            @endif

        </div>

    </main>


    <!-- ============================================================= -->
    <!-- MODAL DE SUCESSO                                              -->
    <!-- ============================================================= -->

    <div
        id="modal-sucesso"
        class="fixed inset-0
               bg-black/70
               backdrop-blur-sm
               flex items-center
               justify-center
               z-50 hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-sucesso"
    >

        <div
            class="glass-panel-strong
                   rounded-3xl p-10
                   max-w-md w-full
                   mx-4 text-center
                   shadow-2xl"
        >

            <div
                class="h-20 w-20
                       bg-emerald-500/10
                       text-emerald-400
                       rounded-full
                       flex items-center
                       justify-center
                       mx-auto mb-6"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
            </div>


            <h3
                id="titulo-modal-sucesso"
                class="text-2xl
                       font-bold
                       text-white mb-2"
            >
                Contratação Realizada!
            </h3>


            <p
                id="mensagem-sucesso"
                class="text-slate-400
                       text-sm mb-6"
            >
                O crédito foi contratado com sucesso.
            </p>


            <div
                class="bg-emerald-500/5
                       border border-emerald-500/10
                       rounded-xl
                       p-3 mb-6
                       text-xs
                       text-emerald-400
                       font-mono"
            >
                Status: CONTRATADO
            </div>


            <a
                href="/"
                class="inline-block
                       px-8 py-3
                       bg-emerald-500/10
                       border border-emerald-500/20
                       text-emerald-400
                       hover:bg-emerald-500/20
                       rounded-xl
                       text-sm font-medium
                       transition-all"
            >
                Iniciar Nova Análise
            </a>

        </div>
    </div>


    <!-- ============================================================= -->
    <!-- FOOTER                                                        -->
    <!-- ============================================================= -->

    <footer
        class="border-t
               border-panelBorder/40
               py-6 text-center
               text-xs text-slate-600"
    >
        <p>
            &copy; 2026 Coop0156.
            Desafio Técnico Laravel.
        </p>
    </footer>


    <!-- ============================================================= -->
    <!-- JAVASCRIPT                                                    -->
    <!-- ============================================================= -->

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /*
             * O ID vem do servidor através do Blade.
             * Não dependemos de parsing manual da URL.
             */
            const analiseId = @json($analise->id);


            /*
             * O botão somente existe para uma análise aprovada.
             *
             * Em estados como "contratado" ou "reprovado",
             * o próprio Blade não renderiza este botão.
             */
            const btnConfirmar =
                document.getElementById('btn-confirmar');


            if (!btnConfirmar) {
                return;
            }


            const txtConfirmar =
                document.getElementById('txt-confirmar');

            const spinnerConfirmar =
                document.getElementById('spinner-confirmar');

            const iconeConfirmar =
                document.getElementById('icone-confirmar');

            const modalSucesso =
                document.getElementById('modal-sucesso');

            const mensagemSucesso =
                document.getElementById('mensagem-sucesso');

            const erroContratacao =
                document.getElementById('erro-contratacao');

            const textoErroContratacao =
                document.getElementById(
                    'texto-erro-contratacao'
                );


            /*
             * Evita uma segunda requisição enquanto
             * a primeira contratação está sendo processada.
             */
            let processando = false;


            /**
             * Controla o estado visual do botão.
             */
            function setLoading(loading) {

                btnConfirmar.disabled = loading;

                if (loading) {

                    btnConfirmar.classList.add(
                        'opacity-70',
                        'cursor-not-allowed'
                    );

                    txtConfirmar.textContent =
                        'Confirmando contratação...';

                    spinnerConfirmar.classList.remove(
                        'hidden'
                    );

                    iconeConfirmar.classList.add(
                        'hidden'
                    );

                    return;
                }


                btnConfirmar.classList.remove(
                    'opacity-70',
                    'cursor-not-allowed'
                );

                txtConfirmar.textContent =
                    'Confirmar Contratação';

                spinnerConfirmar.classList.add(
                    'hidden'
                );

                iconeConfirmar.classList.remove(
                    'hidden'
                );
            }


            /**
             * Remove mensagem de erro anterior.
             */
            function limparErro() {

                textoErroContratacao.textContent = '';

                erroContratacao.classList.add(
                    'hidden'
                );
            }


            /**
             * Exibe uma falha de forma amigável.
             */
            function exibirErro(mensagem) {

                textoErroContratacao.textContent =
                    mensagem;

                erroContratacao.classList.remove(
                    'hidden'
                );

                erroContratacao.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }


            /**
             * Tenta interpretar uma resposta JSON.
             *
             * Em caso de proxy, servidor ou erro inesperado
             * que retorne HTML, não deixamos o JavaScript quebrar.
             */
            async function obterJson(response) {

                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                if (
                    !contentType.includes(
                        'application/json'
                    )
                ) {
                    return {};
                }


                try {

                    return await response.json();

                } catch (erro) {

                    return {};
                }
            }


            /**
             * Constrói uma mensagem adequada de acordo
             * com o status HTTP devolvido pela API.
             */
            function obterMensagemErro(
                response,
                data
            ) {

                /*
                 * A própria API possui mensagens de negócio
                 * para casos como análise não aprovada.
                 */
                if (
                    data &&
                    typeof data.message === 'string' &&
                    data.message.trim() !== ''
                ) {
                    return data.message;
                }


                switch (response.status) {

                    case 404:
                        return 'A análise de crédito não foi encontrada.';

                    case 422:
                        return 'Esta análise não está disponível para contratação.';

                    case 429:
                        return 'Muitas solicitações foram realizadas. Aguarde alguns instantes e tente novamente.';

                    case 500:
                        return 'Ocorreu um erro interno ao processar a contratação.';

                    default:
                        return 'Não foi possível confirmar a contratação. Tente novamente.';
                }
            }


            /**
             * Abre o modal após confirmação da API.
             */
            function exibirSucesso(data) {

                mensagemSucesso.textContent =
                    data?.message
                    ?? 'O crédito foi contratado com sucesso.';

                modalSucesso.classList.remove(
                    'hidden'
                );

                /*
                 * Evita scroll no conteúdo atrás do modal.
                 */
                document.body.classList.add(
                    'overflow-hidden'
                );
            }


            /**
             * Confirma contratação.
             */
            btnConfirmar.addEventListener(
                'click',
                async () => {

                    /*
                     * Proteção adicional contra clique duplo.
                     */
                    if (processando) {
                        return;
                    }


                    processando = true;

                    limparErro();

                    setLoading(true);


                    try {

                        const response = await fetch(
                            `/api/analise-credito/${analiseId}/contratar`,
                            {
                                method: 'POST',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                }
                            }
                        );


                        const data =
                            await obterJson(response);


                        if (!response.ok) {

                            throw new Error(
                                obterMensagemErro(
                                    response,
                                    data
                                )
                            );
                        }


                        /*
                         * Validação defensiva do retorno.
                         *
                         * O backend hoje retorna:
                         *
                         * {
                         *   message: "...",
                         *   analise: {
                         *      status: "contratado"
                         *   }
                         * }
                         */
                        if (
                            !data ||
                            !data.analise ||
                            data.analise.status !== 'contratado'
                        ) {

                            throw new Error(
                                'A contratação foi processada, mas o servidor retornou um estado inesperado.'
                            );
                        }


                        exibirSucesso(data);


                    } catch (erro) {

                        console.error(
                            'Erro ao confirmar contratação:',
                            erro
                        );


                        /*
                         * TypeError normalmente representa
                         * falha de rede no fetch.
                         */
                        const mensagem =
                            erro instanceof TypeError
                                ? 'Não foi possível comunicar com o servidor. Verifique a conexão e tente novamente.'
                                : (
                                    erro.message
                                    || 'Não foi possível confirmar a contratação.'
                                );


                        exibirErro(mensagem);

                        /*
                         * Só permitimos novo clique quando
                         * a operação não foi concluída.
                         */
                        processando = false;

                        setLoading(false);

                    }
                }
            );

        });
    </script>

</body>

</html>