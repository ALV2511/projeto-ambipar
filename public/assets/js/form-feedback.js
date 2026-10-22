const feedback = document.querySelector('[data-form-feedback]');

if (feedback) {
    const error = new URLSearchParams(window.location.search).get('erro');
    const messages = {
        campos: 'Confira os campos informados e tente novamente.',
        cadastro: 'Não foi possível concluir o cadastro. Verifique se o e-mail já está em uso.',
        credenciais: 'E-mail ou senha inválidos.',
        indisponivel: 'Não foi possível acessar sua conta agora. Tente novamente mais tarde.',
    };

    feedback.textContent = messages[error] ?? '';
    feedback.hidden = !messages[error];
}
