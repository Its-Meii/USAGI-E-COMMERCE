console.log();

// let productQuantity = document.querySelectorAll('.productQuantity');
let productQuantityMinus = document.querySelectorAll('.productQuantityMinus');
let productQuantityPlus = document.querySelectorAll('.productQuantityPlus');
let NewproductQuantityValue = 1;

productQuantityMinus.forEach(e =>{
    e.addEventListener('click', (e)=>{
        let parent = e.target.parentNode;
        let productQuantity = parent.parentNode.querySelector('.productQuantity');
        NewproductQuantityValue = productQuantity.value;
        if(NewproductQuantityValue > 1){
            NewproductQuantityValue--;
            e.target.disabled = false;
            parent.querySelector('.productQuantityPlus').disabled = false;
            productQuantity.value = NewproductQuantityValue;
        }
        else{
            e.target.disabled = true;
        }
    });
});

productQuantityPlus.forEach(e =>{
    e.addEventListener('click', (e)=>{
        let parent = e.target.parentNode;
        let productQuantity = parent.querySelector('.productQuantity');
        let productQuantityMax = parseInt(productQuantity.dataset.max);
        NewproductQuantityValue = productQuantity.value;
        if(NewproductQuantityValue >= 1 && productQuantityMax > NewproductQuantityValue){
                NewproductQuantityValue++;
                productQuantity.value = NewproductQuantityValue;      
                parent.querySelector('.productQuantityMinus').disabled = false;        
        }
        else{
            e.target.disabled = true;
        }
    });
});