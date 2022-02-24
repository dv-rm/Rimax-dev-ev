require(['jquery', 'jquery/ui'], function ($) {

    $(window).ready(function () {
        let headerDelete = document.querySelector("#html-body > main > div > header")
        let topheader = $(".top-header-content")

        $(".page-wrapper").before(topheader)

        let logo = $(".logo-header")
        let infoContainer = `<div class="logoContainer"> ${logo.html()} </div>  <div class="infoContainer"> <div> ¿Necesitas ayuda con tu compra?  <a href=""> Llámanos 350 310 0418 </a> </div> <div>  <a href="mailto:info@rimax.com.co"> Escríbenos info@rimax.com.co </a> <div> </div> </div>  </div>`
        $(".breadcrumbs").html(infoContainer)
        $(".breadcrumbs ").addClass("container_header_checkout")
        $(".breadcrumbs ").removeClass("breadcrumbs")

        headerDelete.remove()
        $("#placeholder-header").remove()
        // $( ".breadcrumbs" ).remove()

        try {

            var URLactual = window.location;
            let timeOut = setInterval(() => {
                let loginRemove = document.querySelector("#checkout > div.authentication-wrapper")
                let progressBar = document.querySelector("#checkout > ul > li:nth-child(2)")
                let containerProgressBar = document.querySelector("#checkout > ul")
                let showcart = $(".showcart")
                let showcartContainer = document.querySelector("#shipping > div.step-title")

                if (loginRemove !== null && progressBar !== null && containerProgressBar !== null && showcart !== null) {
                    loginRemove.remove()


                    $(".opc-progress-bar").append(`<li class="opc-progress-bar-item">
                    <span >Listo</span>
                </li>`)
                    showcart.text("Detalles")
                    showcartContainer.append(showcart[0])
                    letProgressBar = document.querySelector("#checkout > ul.opc-progress-bar")
                    clearInterval(timeOut)
                }
            }, 100);

            let methodsShipping = setInterval(() => {
                let containerMethods = document.querySelector("#co-shipping-method-form")

                if (containerMethods !== null) {
                    $("#co-shipping-method-form").append(`<div class="info-shipping"><div class="icon"><span class="iconify" data-icon="akar-icons:info-fill"></div></span> <p> Para ofrecerte un mejor servicio puede que cambie la transportadora según tu ubicación.</p> </div>`)
                    clearInterval(methodsShipping)
                }
            }, 400);



            let getClassCard = setInterval(() => {

                if (URLactual.hash === "#payment") {

                    let typeCard = document.getElementsByClassName("typeCard")
                    let containerGetClass = document.querySelectorAll("#checkout-payment-method-load > div > div:nth-child(1) > .payment-method")
                    let changefin = document.querySelectorAll(".fin")
                    changefin.forEach(element => element.innerHTML = "**** **** ****")

                    if (typeCard.length > 0) {
                        for (let i = 0; i < typeCard.length; i++) {

                            containerGetClass[i].classList.add(typeCard[i].innerHTML)

                        }
                        clearInterval(getClassCard)
                    }
                } else {

                }
            }, 300)

            let styleOfcards = setInterval(() => {
                if (URLactual.hash === "#payment") {
                    let inputNumberCard = document.getElementById("eloom_payments_payu_cc_number")
                    let containerCard = document.getElementById("eloom_payments_payu_cc-form")
                    containerCard.setAttribute("class", `form card-general`)

                    let containerPayments = document.querySelectorAll("#checkout-payment-method-load > div > div.payment-group")

                    if (containerPayments.length > 1) {
                        containerPayments[0].classList.add("saves-cards")
                    } else {

                    }


                    if (inputNumberCard !== null) {

                        inputNumberCard.addEventListener("keypress", function () {
                            let inputOfCard = document.getElementById("eloom_payments_payu_cc_type")
                            let containerCard = document.getElementById("eloom_payments_payu_cc-form")

                            setTimeout(() => {
                                let valorInput = inputOfCard.getAttribute("value")

                                if (valorInput !== null) {
                                    containerCard.setAttribute("class", `form card-general card-save ${valorInput}`)
                                }
                            }, 700);
                        })
                        clearInterval(styleOfcards)
                    }

                } else {

                }

            }, 300);

            let tccsetInterval = setInterval(() => {

                if (URLactual.hash === "#shipping") {

                    let tcc = document.querySelector("#label_carrier_bestway_tablerate");

                    if (tcc !== null) {
                        if (tcc.innerHTML === "tcc") {
                            tcc.innerHTML = `<img class="tcc" src="https://upload.wikimedia.org/wikipedia/commons/a/a8/Logo_TCC.svg"></img>`

                            tcc = document.querySelector("#label_carrier_bestway_tablerate");

                            if (tcc.innerHTML === "tcc") {
                                tcc.innerHTML = `<img class="tcc" src="https://upload.wikimedia.org/wikipedia/commons/a/a8/Logo_TCC.svg"></img>`

                                clearInterval(tccsetInterval)
                            }


                        } else {}

                    }

                } else {}

            }, 300);








        } catch (error) {
            console.error(error)
        }


    })

});
