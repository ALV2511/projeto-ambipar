const painel = document.querySelector('[data-painel]');
const feedback = document.querySelector('[data-painel-feedback]');

if (painel && feedback) {
    const mostrarFalha = (mensagem) => {
        feedback.textContent = mensagem;
        feedback.hidden = false;
    };

    const preencherTexto = (seletor, valor) => {
        const elemento = painel.querySelector(seletor);
        if (elemento) {
            elemento.textContent = valor;
        }
    };

    const carregarPainel = async () => {
        try {
            const resposta = await fetch('api/painel_dados.php', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (resposta.status === 401) {
                window.location.replace('login.html');
                return;
            }

            if (!resposta.ok) {
                throw new Error('Não foi possível carregar seus dados.');
            }

            const dados = await resposta.json();
            preencherTexto('[data-usuario-nome]', dados.usuario.nome);
            preencherTexto('[data-usuario-email]', dados.usuario.email);
            preencherTexto('[data-nivel-numero]', String(dados.nivel.numero));
            preencherTexto('[data-nivel-nome]', dados.nivel.nome);
            preencherTexto('[data-xp-atual]', String(dados.nivel.xp));
            preencherTexto('[data-estatistica-xp]', String(dados.nivel.xp));
            preencherTexto('[data-estatistica-acessos]', String(dados.estatisticas.dias_de_acesso));
            preencherTexto(
                '[data-estatistica-insignias]',
                `${dados.estatisticas.insignias_conquistadas}/${dados.estatisticas.total_insignias}`,
            );
            preencherTexto(
                '[data-insignias-resumo]',
                `${dados.estatisticas.insignias_conquistadas} de ${dados.estatisticas.total_insignias} conquistadas`,
            );

            const progresso = painel.querySelector('[data-progresso]');
            const preenchimento = painel.querySelector('[data-progresso-preenchimento]');
            const percentual = dados.nivel.progresso_percentual;
            if (progresso && preenchimento) {
                progresso.setAttribute('aria-valuenow', String(percentual));
                preenchimento.style.width = `${percentual}%`;
            }

            const proximoNivel = dados.nivel.proximo_nivel_xp;
            preencherTexto(
                '[data-progresso-legenda]',
                proximoNivel === null
                    ? 'Você alcançou o nível máximo. Continue somando XP!'
                    : `${dados.nivel.xp} de ${proximoNivel} XP para o próximo nível`,
            );

            const listaInsignias = painel.querySelector('[data-insignias-lista]');
            if (listaInsignias) {
                listaInsignias.replaceChildren();
                dados.insignias.forEach((insignia) => {
                    const cartao = document.createElement('article');
                    cartao.className = `insignia-card${insignia.conquistada ? ' conquistada' : ''}`;

                    const icone = document.createElement('span');
                    icone.className = 'insignia-icone';
                    icone.setAttribute('aria-hidden', 'true');
                    icone.textContent = insignia.icone;

                    const titulo = document.createElement('h3');
                    titulo.textContent = insignia.nome;

                    const descricao = document.createElement('p');
                    descricao.textContent = insignia.descricao;

                    const estado = document.createElement('span');
                    estado.className = 'insignia-estado';
                    estado.textContent = insignia.conquistada ? 'Conquistada' : 'Bloqueada';

                    cartao.append(icone, titulo, descricao, estado);
                    listaInsignias.append(cartao);
                });
            }

            painel.hidden = false;
        } catch (error) {
            console.error('Dashboard data could not be loaded:', error);
            mostrarFalha('Não foi possível carregar o painel. Atualize a página para tentar novamente.');
        }
    };

    carregarPainel();
}
