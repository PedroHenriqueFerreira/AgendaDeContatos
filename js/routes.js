const routes = (e, re_render = false) => {
    document.querySelector('.indeterminate-disabled').className = 'indeterminate';
    fetch(e).then(result => result.text())
        .then(result => {
            if (result == 'back') {
                window.history.back();
            } else {
                if (re_render == true) {
                    const sidenav = result
                        .split('<ul class="sidenav bg-dark" id="mobile-demo" style="padding-top: 50px; z-index: 9999;">')[1]
                        .split('</ul>')[0];

                    const nav = result
                        .split('<nav class="bg-dark" style="position: fixed; top: 0; z-index: 999">')[1]
                        .split('</nav>')[0];

                    const container = result
                        .split('<div class="container">')[1]
                        .split('<footer>')[0]
                        .slice(0, -7);

                    document.querySelector('.sidenav').innerHTML = sidenav;
                    document.querySelector('nav').innerHTML = nav;
                    document.querySelector('.container').innerHTML = container;

                } else {
                    const loadedPage = result
                        .split('<div class="container">')[1]
                        .split('<footer>')[0]
                        .slice(0, -7);
                    document.querySelector('.container').innerHTML = loadedPage;
                }
                document.querySelector('.indeterminate').className = 'indeterminate-disabled';
                var elem = document.querySelectorAll('.sidenav');
                var instances = M.Sidenav.init(elem);
                try {
                  instances.close();
                } catch(e) {
                  
                }
            }
        });

}

window.addEventListener('popstate', pop => {
    routes(location.pathname);
});

export default routes;
