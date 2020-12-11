import routes from './../routes.js';

const submitEvent = e => {
    e.preventDefault();

    if (e.target.id == 'login') {
        const email = e.target.querySelector('input#email').value;
        const password = e.target.querySelector('input#password').value;

        if (!email || !password) return M.toast({ html: 'Preencha todos os campos' });

        if (!email.match('.com') || !email.match('@') || email.match('@.com')) return M.toast({ html: 'Email inválido' });

        if (password.length < 5 || password.length > 32) return M.toast({ html: 'A senha precisa ter entre 5 e 32 caracteres' });

        const form = new FormData();

        form.append('email', email);
        form.append('password', password);

        document.querySelector('.indeterminate-disabled').className = 'indeterminate';

        fetch('/login/request', {
                method: 'POST',
                body: form
            }).then(result => result.text())
            .then(result => {
                document.querySelector('.indeterminate').className = 'indeterminate-disabled';
                if (result == '200') {
                    window.history.pushState('/', '/', '/');
                    routes('/', true);
                    M.toast({ html: 'Login feito com sucesso' });
                } else {
                    M.toast({ html: result });
                }
            });
    }

    if (e.target.id == 'register') {
        const name = e.target.querySelector('input#name').value;
        const email = e.target.querySelector('input#email').value;
        const password = e.target.querySelector('input#password').value;
        const confirmPassword = e.target.querySelector('input#confirm-password').value;

        if (!name || !email || !password || !confirmPassword) return M.toast({ html: 'Preencha todos os campos' });

        if (name.length < 3 || name.length > 50) return M.toast({ html: 'O nome precisa ter entre 3 e 50 caracteres' });

        if (!email.match('.com') || !email.match('@') || email.match('@.com')) return M.toast({ html: 'Email inválido' });

        if (password.length < 5 || password.length > 32) return M.toast({ html: 'A senha precisa ter entre 5 e 32 caracteres' });

        if (confirmPassword !== password) return M.toast({ html: 'As senhas não conferem' });

        const form = new FormData();

        form.append('name', name);
        form.append('email', email);
        form.append('password', password);
        form.append('confirm_password', confirmPassword);

        document.querySelector('.indeterminate-disabled').className = 'indeterminate';

        fetch('/register/request', {
                method: 'POST',
                body: form
            }).then(result => result.text())
            .then(result => {
                document.querySelector('.indeterminate').className = 'indeterminate-disabled';
                if (result == '200') {
                    window.history.pushState('/login', '/login', '/login');
                    routes('/login');
                    M.toast({ html: 'Usuário criado com sucesso!' });
                } else {
                    M.toast({ html: result });
                }
            });
    }

    if (e.target.id == 'contact_add' || e.target.id == 'contact_edit') {
        const name = e.target.querySelector('input#name').value;
        const email = e.target.querySelector('input#email').value;
        const address = e.target.querySelector('input#address').value;
        const photo = e.target.querySelector('input#photo').files[0] ?
            e.target.querySelector('input#photo').files[0] : '';

        if (!name || !email) return M.toast({ html: 'Preencha todos os campos' });

        if (name.length < 3 || name.length > 50) return M.toast({ html: 'O nome precisa ter entre 3 e 50 caracteres' });

        if (!email.match('.com') || !email.match('@') || email.match('@.com')) return M.toast({ html: 'Email inválido' });

        const form = new FormData();

        const numbers = e.target.querySelectorAll('input[type="tel"]');
        const lastNumber = parseInt(numbers[numbers.length - 1].id.replace('number_', ''));
        const numArray = [];
        for (let i = 1; i <= lastNumber; i++) {

            if (e.target.querySelector(`#number_${i}`)) {
                let number = e.target.querySelector(`#number_${i}`);

                if (!number.value) {
                    return M.toast({ html: 'Preencha todos os campos' });
                }

                if (number.value.length !== 19 || number.value.indexOf('+') == -1 || number.value.indexOf('-') == -1 || number.value.indexOf('(') == -1 || number.value.indexOf(')') == -1) {
                    return M.toast({ html: 'Número inválido' });
                }

                numArray.push(number.value);

                form.append(`number_${i}`, number.value);
            }
        }

        const noRepeatedNumbers = [...new Set(numArray)];

        if (noRepeatedNumbers.join('') != numArray.join('')) {
            return M.toast({ html: 'Cada número tem que ser único' });
        }

        form.append('name', name);
        form.append('email', email);
        form.append('address', address);
        form.append('photo', photo);

        if (e.target.id == 'contact_add') {
            form.append('is_new', 'true');
            form.append('id', '');
        } else {
            form.append('is_new', 'false');
            const id = e.target.querySelector('input#hidden').value;
            form.append('id', id);
        }

        document.querySelector('.indeterminate-disabled').className = 'indeterminate';

        fetch('/contact/add/request', {
                method: 'POST',
                body: form
            }).then(result => result.text())
            .then(result => {
                document.querySelector('.indeterminate').className = 'indeterminate-disabled';
                if (result == '200') {
                    window.history.pushState('/', '/', '/');
                    routes('/');
                    if (e.target.id == 'contact_add') {
                        M.toast({ html: 'Contato criado com sucesso!' });
                    } else {
                        M.toast({ html: 'Contato editado com sucesso!' });
                    }
                } else {
                    M.toast({ html: result });
                }
            });
    }
}

export default submitEvent;
