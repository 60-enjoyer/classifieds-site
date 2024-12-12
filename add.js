$("#confirm-create").on("click", () => {
    const formData = new FormData();
    formData.append("isAddAdvt", true);
    formData.append("caption", $("#caption").val());
    formData.append("cost", $("#cost").val());
    formData.append("category", $("#cur-category").val());
    formData.append("description", $("#description").val());
    if (selectedFile) {
        formData.append("image", selectedFile); // Добавляем файл
    }

    // Отправляем FormData с помощью AJAX
    $.ajax({
        url: "./index.php",
        method: "POST",
        data: formData,
        processData: false, // Отключаем преобразование данных в строку
        contentType: false, // Отключаем установку заголовка Content-Type
        success: (response) => {
            if(response === "success") window.location.href = "."
        },
        error: (error) => {
            console.error("Error:", error);
        },
    });
})

$('#pic').on('change', function (event) {
    const image = document.getElementById('preview');
    selectedFile = event.target.files[0];
    if (selectedFile) {
        image.src = URL.createObjectURL(selectedFile); // Показываем предпросмотр изображения
    }
});

function parseCategories(){
    $.post("./index.php", {
        isGetCategories:true
    }, (data)=>{
        var list = document.getElementById("dc")
        list.innerHTML = "";
        JSON.parse(data).forEach(el => {
            const categoryId = `category-${el[0]}`; // Уникальный ID
            list.innerHTML += `<div id="${categoryId}" class="category unselectable div-btn" data-category-name="${el[1]}">${el[1]}</div>`;
        });

        // Делегируем обработчик клика на общий контейнер
        $(list).on("click", ".category", function () {
            const categoryName = $(this).data("category-name");
            $("#cur-category").val(categoryName);
        });
        
    })
}

parseCategories()