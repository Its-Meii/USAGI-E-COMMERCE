    let urlApi = 'https://api-adresse.data.gouv.fr/search/?q='
    let city = document.querySelector("#order_city")
    let address = document.querySelector('#order_address')
    let zipcode = document.querySelector('#order_zipcode')
    // console.log('ready');
    const datalist = document.createElement("datalist");
    datalist.id="cities";
    address.parentElement.append(datalist)
    
    address.addEventListener("input", function (e) {
        if(e.target.value.length>3){
            fetch(`${urlApi}${address.value}&limit=10`)
            .then(response => response.json())
            .then(data => {
                datalist.innerHTML = ''
                for(const citie of data.features){
                    if(data.features.length === 1){
                        // console.log('trouve');
                        let cityZipcode = citie.properties.postcode;
                        let cityName = citie.properties.city;
                        zipcode.value = cityZipcode;
                        city.value =cityName;
                    }    
                    let addressName =  citie.properties.label;
                    const option = document.createElement("option")
                    option.value = addressName;
                    option.textContent = addressName;
                    datalist.appendChild(option)          
                }
            })
        }  
    })