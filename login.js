var r = document.querySelector('*')
r.style.setProperty("--form-clickable", "all")
r.style.setProperty("--shift", "50%")
r.style.setProperty("--swap-z-index", "-1")


$("#login-button").on("click", () => {
    $.post("./index.php", {
        isLogin: true,
        login: $("#val-login").val(),
        password: $("#val-password").val()
    }, (data)=>{
        data = JSON.parse(data)
        if(data["status"] == "success") document.location.reload()
    })
})

$("#registr-button").on("click", () => {
    $.post("./index.php", {
        isRegister: true,
        login: $("#reg-login").val(),
        password: $("#reg-password").val()
    }, (data)=>{
        data = JSON.parse(data)
        if(data["status"] == "success") document.location.reload()
    })
})


$("#swap-to-login").on("click", () => {
    r.style.setProperty("--form-clickable", "none")
    r.style.setProperty("--shift", "30%")
    setTimeout(()=>{
        r.style.setProperty("--swap-z-index", "-1")
        r.style.setProperty("--shift", "50%")
        setTimeout(() => {
            r.style.setProperty("--form-clickable", "all")
        }, 500)
    }, 500)
})


$("#swap-to-registration").on("click", () => {
    r.style.setProperty("--form-clickable", "none")
    r.style.setProperty("--shift", "70%")
    setTimeout(()=>{
        r.style.setProperty("--swap-z-index", "1")
        r.style.setProperty("--shift", "50%")
        setTimeout(() => {
            r.style.setProperty("--form-clickable", "all")
        }, 500)
    }, 500)
})