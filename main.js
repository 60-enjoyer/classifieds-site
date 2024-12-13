$("#create-advt").on("click", () => {window.location.href = ".?add"})

$("#exit-button").on("click", () => {
    $.post("./index.php", {
        isExit:true
    }, ()=>{document.location.reload()})
})

let timer;
let advtData;
const delay = 1000;

$('#search-field').on('input', function() {
    clearTimeout(timer);
    const value = $(this).val();

    timer = setTimeout(() => {
        addContent(value)
    }, delay);
});

function addContent(search){
    var list = document.getElementById("advt-aera")
    list.innerHTML = ""
    advtData.forEach(el => {
        var template = `<div class="advt-chrome-bulshit">
                <div class="advt-img" style="background-image: url('uploads/${el[2]}')"> </div>
                <div class="advt-text">
                    <div class="advt-header">
                        <p class="advt-caption">${el[3]}</p>
                        <p class="advt-price">${el[5]}$</p>
                    </div>
                    <p calss="advt-description">${el[4]}</p>
                </div>
            </div>`
        if(el[3].includes(search)){
            list.innerHTML += template;
        } else if(search == null) {
            list.innerHTML += template;
        }
    });
}

function loadAdvt(cat){
    $.post(".", {
        isGetAdvt: true,
        cat: cat
    }, (data)=>{
        console.log(data)        
        advtData = JSON.parse(data);
        addContent()
    })
}

loadAdvt()



$.post("./index.php", {
    isGetCategories:true
}, (data)=>{
    var list = document.getElementById("dc")
    list.innerHTML = "";
    list.innerHTML += `<div id="-1" class="category unselectable div-btn" data-category-name="all">Всі категорії</div>`
    JSON.parse(data).forEach(el => {
        const categoryId = `category-${el[0]}`; // Уникальный ID
        list.innerHTML += `<div id="${categoryId}" class="category unselectable div-btn" data-category-name="${el[1]}">${el[1]}</div>`;
    });

    // Делегируем обработчик клика на общий контейнер
    $(list).on("click", ".category", function () {
        const categoryName = $(this).data("category-name");
        $("#cur-category").text(categoryName);
        categoryName === "all" ? loadAdvt() : loadAdvt(categoryName)
    });
    
})
