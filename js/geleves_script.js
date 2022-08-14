const form = document.getElementById('form-inscription');
const form2 = document.getElementById('formulaire-connexion');
const button = document.querySelector('.bout-sub');
const load = document.createElement("img");
const arrayInput = document.querySelectorAll('.cont_left input:not(input[type="radio"])');
const checkInput = document.querySelectorAll('.geleves-cont-form-inscription .cont_left input[type="radio"]');
const sectionGeleveForm = document.querySelector('.geleves-cont-form-inscription form');
const checkInputAbo = document.querySelectorAll('.geleves-cont-form-inscription .cont_formules input[type="radio"]');
const messagesValid = document.createElement("p")
const messagesValid2 = document.createElement("span")
const titreValid = document.createElement("h2")
const contError = document.querySelector('.message-valid');
const date = document.querySelector('input[type="date"]');

let check = []
let check2 = []
let value = []

console.log(myScript)

load.setAttribute('src', myScript.gestion + '/MMA-plugin/img/load2.svg')


form.addEventListener("submit", function test(e) {
    e.preventDefault();

    button.textContent = "";
    button.append(load);
    button.setAttribute("disabled", "disabled");

    //  BOUCLES POUR SELECTIONNER LES INPUTS //
    arrayInput.forEach(element => {
        value.push(element)
    });
    checkInput.forEach(element => {
        check.push(element)
    });
    checkInputAbo.forEach(element => {
        check2.push(element)
    });
    //////////

    let data = new FormData(this);

    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {

            console.log(date)
            console.log(value)
            console.log(check)
            console.log(check2)
            //////////////////////////////////////////////////////////////////////////

            let res = this.response;
            if (res == null && value.every(x => x != "") && check.some(x => x.checked) && check2.some(x => x.checked) && date.value != "") {
                // SI LE CHAMP EST BON, ON APPLIQUE ICI

                // arrayInput.forEach(element => {
                //         element.value = "";
                //         element.classList.remove('champ-error')
                // });

                titreValid.textContent = "Félicitation !!!"
                messagesValid.textContent = "Votre inscription à bien été envoyé."
                contError.append(titreValid);
                contError.append(messagesValid);
                messagesValid2.remove();
                // form.reset();     
                contError.classList.add('activate');
                sectionGeleveForm.classList.add('activate');

                load.remove();
                button.textContent = "Envoyer";
                button.removeAttribute('disabled')
            } else {
                // SI LE CHAMP EST REFUSE, ON APPLIQUE ICI

                for (let i = 0; i < arrayInput.length; i++) {
                    if (arrayInput[i].value == "" || arrayInput[i].value === undefined) {
                        arrayInput[i].classList.add('champ-error')
                    } else {
                        arrayInput[i].classList.remove('champ-error')
                    }
                }

                titreValid.textContent = "Erreur"
                messagesValid.textContent = "Veuillez remplir les tout les champs"
                contError.append(titreValid);
                contError.append(messagesValid);
                if (!check.some(x => x.checked)) {
                    messagesValid2.textContent = "Veuillez sélectionner votre type d'abonnement"
                    contError.append(messagesValid2)
                }

                load.remove();
                button.textContent = "Envoyer";
                button.removeAttribute('disabled')
            }

            //////////////////////////////////////////////////////////////////////////////////

        } else if (this.readyState == 4) {
            console.log('Le mail n\'a pas pu être envoyé, veuillez essayer ultérieurement.');
        }
    };

    xhr.open("POST", myScript.gestion + '/MMA-plugin/action.php', true); // Relis l'url d'envoie à celui qui est appelé dans ge_function.php
    xhr.responseType = "json";
    xhr.send(data);

    return false
});




// permet de filtrer les caractères entrés (uniquelent nombre) et de mettre un espace tout les 2 chiffres

let tel = document.querySelector('input[name="tel"]');
tel.addEventListener("input", function (e) {
    e.preventDefault()

    let number = tel.value;
    if (number.length > 0) {
        number = number.replace(/\D/g, '');

        if (number.length > 1) {
            number = number.match(/\d{1,2}/g).join(" ");
        }
        tel.value = number
    }
});


