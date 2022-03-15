episodes=[]
episodesFiltre=[]
GlobalTarget=""
dateStart=""
dateEnd=""
nom=""


function deleteRow(){
    Array.from(document.getElementById('episodes').children).forEach(function(e){
        e.remove()
    })
}
function addCol(val,row,liste,min,max){
    
    
    i=0
    for(item in val){
        if(i!=0){
            newCol=document.createElement('th')
            newCol.style="text-align: center; vertical-align: middle;"
            
            if(typeof(val[item])===typeof(val['date'])){
                //console.log(val[item])
                subDate=val[item].date.substring(0,10).split('-')
                dateTemp=splitDate[2]+"/"+splitDate[1]+"/"+splitDate[0]
                newCol.innerHTML=temp
            }
            else{
                newCol.innerHTML=val[item]
            }
            if(liste.indexOf(val)>=min && liste.indexOf(val)<max){
                row.appendChild(newCol)
            }
        }
        
        i++
    }
    
    
}
function pager(liste){
    Array.from(document.getElementById('pages').children).forEach(function(e){
        e.remove()
    })
    
    nbPage=Math.floor(liste.length/10)
    
    pages=document.getElementById('pages')
    prev=document.createElement('li')
    prev.setAttribute('class',"page-item disabled")
    min=0
    max=10
    buttonPrev=document.createElement('span')
    buttonPrev.setAttribute('class',"page-link")
    buttonPrev.setAttribute('id',"prev")
    buttonPrev.type="button"
    buttonPrev.setAttribute('onclick','modif(0,10,1,window.episodes)')
    buttonPrev.innerHTML="&laquo;&nbsp;Previous"
    prev.appendChild(buttonPrev)
    pages.appendChild(prev)
    
    o=0
    for(i=0; i<=nbPage;i++){
        o=i
        page=document.createElement('li')
        page.setAttribute('class',"page-item")
        buttonPage=document.createElement('span')
        buttonPage.setAttribute('class',"page-link")
        buttonPage.setAttribute('onclick','modif('+min+','+max+','+(i+1)+",window.episodes"+')')
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
    buttonNext.setAttribute('onclick','modif('+(min-10)+','+(max-10)+','+o+",window.episodes"+')')
    buttonNext.innerHTML="Next&nbsp;&raquo;"

    next.appendChild(buttonNext)
    pages.appendChild(next)
    
}


function filtre(min,max){
    
    
    deleteRow()    
    window.episodesFiltre.forEach(function(e){
        
        row=document.createElement('tr')
        splitDate=e['date'].date.substring(0,10).split('-')
        temp=splitDate[2]+"/"+splitDate[1]+"/"+splitDate[0]
            
            
        addCol(e,row,window.episodesFiltre,min,max)
       
        if(row.children['length']!=0){
            window.tbody.appendChild(row)
        }
          
    })
    
    pager(window.episodesFiltre)
    
        
        
}

function modif(min,max,link,liste){
    temp=liste
    window.episodesFiltre=[]
    temp.forEach(function(e){
        if(verif(e)){
            window.episodesFiltre.push(e)
        }
    })
    filtre(min,max)
    Array.from(document.getElementById('pages').children).forEach(function(e){
        e.setAttribute("class","page-item")
    })
    document.getElementById('pages').children[link].setAttribute('class','page-item active')
    
    if(min!=0 && max!=10){
        document.getElementById("prev").parentElement.setAttribute('class','page-item')
        document.getElementById("prev").setAttribute('onclick','modif('+(min-10)+','+(max-10)+','+(link-1)+",window.episodes"+")")
    }
    else{
        document.getElementById("prev").parentElement.setAttribute('class','page-item disabled')
    }
    if(max==(10*Math.ceil(window.episodesFiltre.length/10))){
        document.getElementById("next").parentElement.setAttribute('class','page-item disabled')
    }
    else{
        document.getElementById("next").parentElement.setAttribute('class','page-item')
        document.getElementById("next").setAttribute('onclick','modif('+(min+10)+','+(max+10)+','+(link+1)+",window.episodes"+")")
    }
    
    
}
function trie(col, reverse){
    sortListe=[]
    tempListe=[]
    window.episodesFiltre=[]
    window.episodes.forEach(function(e){
        if(verif(e)){
            tempListe.push(e)
            sortListe.push(e[col])
        }
    })
    sortListe.sort()
    if(reverse){
        sortListe.reverse()
    }
    console.log(window.episodesFiltre)
    sortListe.forEach(function(nom){
        tempListe.forEach(function(e){
            if(e[col]==nom && !window.episodesFiltre.includes(e)){
                window.episodesFiltre.push(e)
            }
        })
    })
    console.log(window.episodesFiltre)
    modif(0,10,1,window.episodesFiltre)
}


