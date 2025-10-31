/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import * as bootstrap from 'bootstrap';
// Exponer Bootstrap globalmente para que la función existente pueda usar `bootstrap.Toast`
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    const toastTrigger = $('liveToastBtn')
    const toastLiveExample = $('liveToast')

    if (toastTrigger) {
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
        toastTrigger.addEventListener('click', () => {
            toastBootstrap.show()
        })
    }
})
