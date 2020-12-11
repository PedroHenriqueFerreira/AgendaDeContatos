import routes from './../routes.js';

const clickEvent = e => {

  if (e.target.tagName == 'A') {
    e.preventDefault();
    switch (e.target.getAttribute('href')) {
      case '/logout':
        e.preventDefault();
        fetch('/logout').then(result => result.text())
          .then(result => {
            window.history.pushState('/', '/', '/');
            routes('/', true);
            M.toast({ html: 'Você saiu do sistema' });
          });
        break;
      case '/delete':
        e.preventDefault();
        const url = e.target.getAttribute('url');
        fetch(url).then(result => result.text())
          .then(result => {
            console.log(result);
            if (result == '200') {
              if (e.target.parentElement.parentElement.parentElement.querySelectorAll('tr').length == 1) {
                e.target.parentElement.parentElement.parentElement.parentElement.remove();
                document.querySelector('h3').insertAdjacentHTML('afterend', '<p>Sem contatos cadastrados no momento</p>');
              } else {
                e.target.parentElement.parentElement.remove();
              }
              M.toast({ html: 'Contato excluído com sucesso' });
            } else {
              M.toast({ html: result });
            }
          });
        break;
      case '#':
        e.preventDefault();
        break;
      default:
        e.preventDefault();
        const redirect = e.target.getAttribute('href');
        window.history.pushState(redirect, redirect, redirect);
        routes(redirect);
        break;
    }
    if (e.target.className.match('add_number')) {
      let number = document.querySelectorAll('input')[document.querySelectorAll('input').length - 3].id.replace('number_', '');
      number = parseInt(number) + 1;

      const div = `<div class="input-field"><input id="number_${number}" type="tel" maxlength="19"><label for="number_${number}">Número</label></div>`;

      const a = '<a href="#" class="btn btn-light remove_number" style="font-size: 12px; text-align: right; margin-top: -15px"><i class="material-icons right" style="margin-left: 4px;">delete</i> Remover</a>';

      e.target.insertAdjacentHTML('beforebegin', div);
      e.target.insertAdjacentHTML('beforebegin', a);

      document.querySelectorAll('input')[document.querySelectorAll('input').length - 3].focus();
    }

    if (e.target.className.match('remove_number')) {
      e.target.previousElementSibling.remove();
      e.target.remove();

      document.querySelectorAll('input')[document.querySelectorAll('input').length - 3].focus();
    }
  }

}

export default clickEvent;
