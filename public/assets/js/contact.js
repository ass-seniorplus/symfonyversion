const form = document.querySelector('form');
const statusTxt = form.querySelector('.button-area span');

// Générer une opération aléatoire et l'afficher
const captchaOperation = generateCaptchaOperation();
document.getElementById("captchaOperation").textContent = captchaOperation;

// Extraire les valeurs du captcha et les définir dans les champs cachés
const [num1, operator, num2] = captchaOperation.match(/\d+|[^\d\s]/g);
document.getElementById("captchaNum1").value = num1;
document.getElementById("captchaNum2").value = num2;
document.getElementById("captchaOperator").value = operator;

// Ajouter la vérification du captcha côté client
form.onsubmit = (e) => {
    e.preventDefault();
    const captchaInput = document.getElementById("validationCustomCaptcha");
    const captchaResult = eval(captchaOperation.split('=')[0].trim()); // Calculer la réponse attendue

    if (parseInt(captchaInput.value) === captchaResult) {
        statusTxt.style.color = "#0D6EFD";
        statusTxt.style.display = "block";
        statusTxt.innerText = "Envoi du message en cours...";
        form.classList.add("disabled");

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "contact.php", true);
        xhr.onload = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                let response = xhr.response;
                if (response.indexOf("required") !== -1 || response.indexOf("valid") !== -1 || response.indexOf("failed") !== -1) {
                    statusTxt.style.color = "red";
                } else {
                    form.reset();
                    // Réinitialiser le captcha pour le prochain envoi
                    document.getElementById("captchaOperation").textContent = generateCaptchaOperation();
                    document.getElementById("captchaNum1").value = num1;
                    document.getElementById("captchaNum2").value = num2;
                    document.getElementById("captchaOperator").value = operator;
                    captchaInput.classList.remove("is-invalid", "is-valid");
                    setTimeout(() => {
                        statusTxt.style.display = "none";
                    }, 5000);
                }
                statusTxt.innerText = response;
                form.classList.remove("disabled");
            }
        }

        let formData = new FormData(form);
        xhr.send(formData);
    } else {
        captchaInput.classList.add("is-invalid");
        captchaInput.classList.remove("is-valid");
        // Afficher un message d'erreur pour la réponse au captcha incorrecte
        statusTxt.style.color = "red";
        statusTxt.style.display = "block";
        statusTxt.innerText = "La réponse au captcha est incorrecte.";
    }
};

// Générer une opération aléatoire et l'afficher
function generateCaptchaOperation() {
    const num1 = Math.floor(Math.random() * 10);
    const num2 = Math.floor(Math.random() * 10);
    const operator = ['+', '-', '*'][Math.floor(Math.random() * 3)];
    return `${num1} ${operator} ${num2} = ?`;
}