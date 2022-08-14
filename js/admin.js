window.addEventListener('DOMContentLoaded', function(){

    let boutAide = document.querySelector('.cont-bout-aide-geleves button');
    let contAide = document.querySelector('.cont-aide-geleves');
    let validation = document.querySelectorAll('.valid.column-valid');

    boutAide.addEventListener('click', function(){
        boutAide.classList.toggle('active')
        contAide.classList.toggle('active')
    })



    validation.forEach(e => {
        if(e.textContent === "en_attente"){
            e.style.color = "orange";
        }else{
            e.style.color = "green";
        }
    });
})




