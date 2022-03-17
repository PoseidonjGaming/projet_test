ListeBase=[]
ListeFitre=[]
exception=[]
tbody=""
boolExport=""
listeExport=[]


function deleteRow(){
    Array.from(window.tbody.children).forEach(function(e){
        e.remove()
    })
}

function pager(liste){
    min=0
    max=10
    
    nbPage=Math.floor(liste.length/10)
    
    pages=document.getElementById('pages')
    prev=document.createElement('li')
    prev.setAttribute('class',"page-item disabled")
    
    buttonPrev=document.createElement('span')
    buttonPrev.setAttribute('class',"page-link")
    buttonPrev.setAttribute('id',"prev")
    buttonPrev.type="button"
    buttonPrev.setAttribute('onclick','modif(0,10,1,window.ListeFitre)')
    buttonPrev.innerHTML="&laquo;&nbsp;Previous"
    prev.appendChild(buttonPrev)
    pages.appendChild(prev)
    
    o=0
    for(i=0; i<=nbPage;i++){
        o=i
        page=document.createElement('li')
        
        if(i==0){
            page.setAttribute('class',"page-item active")
        }
        else{
            page.setAttribute('class',"page-item")
        }
        buttonPage=document.createElement('span')
        buttonPage.setAttribute('class',"page-link")
        buttonPage.setAttribute('onclick','modif('+min+','+max+','+(i+1)+",window.ListeFitre"+')')
        buttonPage.innerHTML=i+1
        page.appendChild(buttonPage)
        pages.appendChild(page)
        min=min+10
        max=max+10
        
        
    }

    next=document.createElement('li')
    next.setAttribute('class',"page-item")
    buttonNext=document.createElement('span')
    buttonNext.setAttribute('class',"page-link")
    buttonNext.setAttribute('id',"next")
    buttonNext.type="button"
    buttonNext.setAttribute('onclick','modif('+(min-10)+','+(max-10)+','+o+",window.ListeFitre"+')')
    buttonNext.innerHTML="Next&nbsp;&raquo;"

    next.appendChild(buttonNext)
    pages.appendChild(next)
    
}




function modif(min,max,link,liste){
    temp=liste
    window.ListeFitre=[]
    temp.forEach(function(e){
        if(verif(e)){
            window.ListeFitre.push(e)
        }
    })
    Array.from(document.getElementById('pages').children).forEach(function(e){
        e.remove()
    })
    
    filtre(min,max)
    if(window.boolExport==false){
        Array.from(document.getElementById('pages').children).forEach(function(e){
            e.setAttribute("class","page-item")
        })
        document.getElementById('pages').children[link].setAttribute('class','page-item active')
        
        if(min!=0 && max!=10 && window.ListeFitre.length>10){
            document.getElementById("prev").parentElement.setAttribute('class','page-item')
            document.getElementById("prev").setAttribute('onclick','modif('+(min-10)+','+(max-10)+','+(link-1)+",window.ListeFitre"+")")
        }
        else{
            document.getElementById("prev").parentElement.setAttribute('class','page-item disabled')
        }
        if(max==(10*Math.ceil(window.ListeFitre.length/10)) || window.ListeFitre.length<10){
            document.getElementById("next").parentElement.setAttribute('class','page-item disabled')
        }
        else{
            document.getElementById("next").parentElement.setAttribute('class','page-item')
            document.getElementById("next").setAttribute('onclick','modif('+(min+10)+','+(max+10)+','+(link+1)+",window.ListeFitre"+")")
        }
      
    }
    
    
    
}
function trie(col, reverse){
    sortListe=[]
    tempListe=[]
    window.ListeFitre=[]
    
    window.ListeBase.forEach(function(e){
        if(verif(e)){
            tempListe.push(e)
            sortListe.push(e[col])
        }
    })
    sortListe.sort()
    if(reverse){
        sortListe.reverse()
    }
    
    sortListe.forEach(function(nom){
        tempListe.forEach(function(e){
            if(e[col]==nom && !window.ListeFitre.includes(e)){
                window.ListeFitre.push(e)
            }
        })
    })
   
    modif(0,10,1,window.ListeFitre)
}

function addCol(val,row,liste,min,max){
    for(item in val){
        if(!window.exception.includes(item)){
            newCol=document.createElement('td')
            newCol.style="text-align: center; vertical-align: middle;"
                
            if(typeof(val[item])===typeof(val['date'])){
                subDate=val[item].date.substring(0,10).split('-')
                dateTemp=splitDate[2]+"/"+splitDate[1]+"/"+splitDate[0]
                newCol.innerHTML=temp
            }
            else{
                newCol.innerHTML=val[item]
            }
            
            if(liste.indexOf(val)>=min && liste.indexOf(val)<max || window.boolExport){
                row.appendChild(newCol)
            }
        }
    }
    
    
}

