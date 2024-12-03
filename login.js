$("#login-button").on("click", () => {
    console.log({
        login: $("#val-login").val(),
        password: $("#val-password").val()
    })
    $.post("./index.php", {
        isLogin: true,
        login: $("#val-login").val(),
        password: $("#val-password").val()
    }, (data)=>{
        console.log(data)
        if(data == "succes") document.location.reload()
    })
})