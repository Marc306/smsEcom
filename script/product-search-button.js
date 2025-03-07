// const dropDownSearch = document.querySelector(".right-section2");
// document.querySelector(".burger-imge").addEventListener("click", () =>{
//     dropDownSearch.classList.toggle("right-section2-height");

//     filterSide.classList.remove("side-bar-filter-remove");
// });
// window.addEventListener("resize", () =>{
//     if(window.innerWidth > 900){
//         dropDownSearch.classList.remove("right-section2-height");
//     }
// });



const pointImage = document.querySelector(".point");
pointImage.addEventListener("mouseover", () => {
    pointImage.src = "image/icon/more.png"; 
});

pointImage.addEventListener("mouseout", () => {
    pointImage.src = "image/icon/point.png"; 
});

const filterSide = document.querySelector(".side-bar-filter");
pointImage.addEventListener("click", () =>{
    filterSide.classList.toggle("side-bar-filter-remove");

    // dropDownSearch.classList.remove("right-section2-height");
});
