import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './aos.js';

if(window.innerWidth >= 901){
    let dropdown = document.querySelector('.dropdown');
    let dropbtn = document.querySelector('.dropbtn');
    let dropdownContent = document.querySelector('.dropdown-content');
    
    dropdown.addEventListener('mouseover', ()=>{
            dropbtn.classList.add('active');
            dropbtn.querySelector('i').className="fa-solid fa-chevron-up";
            dropdownContent.classList.add('active');
        
    })
    dropdown.addEventListener('mouseout', ()=>{
            dropbtn.classList.remove('active');
            dropdownContent.classList.remove('active');
            dropbtn.querySelector('i').className="fa-solid fa-chevron-down";
     
    })  
}
if(window.innerWidth < 901){
    let dropbtn = document.querySelector('.dropbtn');
    let dropdownContent = document.querySelector('.dropdown-content');
    let check = false;
    
    dropbtn.addEventListener('click', ()=>{
        if(check){
            dropbtn.classList.remove('active');
            dropdownContent.classList.remove('active');
            dropbtn.querySelector('i').className="fa-solid fa-chevron-down";
            check = false;
        }
        else{
            dropbtn.classList.add('active');
            dropbtn.querySelector('i').className="fa-solid fa-chevron-up";
            dropdownContent.classList.add('active');
            check = true;
        }
    })  
}
