episodes=[]
episodesFiltre=[]
GlobalTarget=""
date=""
nom=""
tbody=document.getElementById('episodes')

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
    buttonPrev.setAttribute('onclick','modif(0,10,1)')
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
        buttonPage.setAttribute('onclick','modif('+min+','+max+','+(i+1)+')')
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
    buttonNext.setAttribute('onclick','modif('+(min-10)+','+(max-10)+','+o+')')
    buttonNext.innerHTML="Next&nbsp;&raquo;"

    next.appendChild(buttonNext)
    pages.appendChild(next)
    
}
function verif(e){
    bool=true
    
    if(window.nom!=""){
       bool=bool && e['nom'].includes(window.nom)
    }
    if(window.GlobalTarget!="vide"){
       
       bool=bool && e['serieId']==window.GlobalTarget
    }
    /*if(window.date!==""){
       bool=bool && e['date']==window.date
    }*/
    
    return bool
    
    
}

function filtre(min,max){
    
    
    deleteRow()    
    window.episodesFiltre.forEach(function(e){
        
        row=document.createElement('tr')
        splitDate=e['date'].date.substring(0,10).split('-')
        temp=splitDate[2]+"/"+splitDate[1]+"/"+splitDate[0]
            
            
        addCol(e,row,window.episodesFiltre,min,max)
            
        window.tbody.appendChild(row)  
    })
    
    pager(window.episodesFiltre)
    
        
        
}

function modif(min,max,link){
    filtre(min,max)
    Array.from(document.getElementById('pages').children).forEach(function(e){
        e.setAttribute("class","page-item")
    })
    document.getElementById('pages').children[link].setAttribute('class','page-item active')
    
    if(min!=0 && max!=10){
        document.getElementById("prev").parentElement.setAttribute('class','page-item')
        document.getElementById("prev").setAttribute('onclick','modif('+(min-10)+','+(max-10)+','+(link-1)+")")
    }
    else{
        document.getElementById("prev").parentElement.setAttribute('class','page-item disabled')
    }
    if(max==(10*Math.ceil(window.episodesFiltre.length/10))){
        document.getElementById("next").parentElement.setAttribute('class','page-item disabled')
    }
    else{
        document.getElementById("next").parentElement.setAttribute('class','page-item')
        document.getElementById("next").setAttribute('onclick','modif('+(min+10)+','+(max+10)+','+(link+1)+")")
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
    
    sortListe.forEach(function(nom){
        
        tempListe.forEach(function(e){
            if(e[col]==nom){
                window.episodesFiltre.push(e)
            }
        })
    })
    modif(0,10,1)
}
$('#ep').change(function(e){
    target=e.target.value
    window.GlobalTarget=target
    window.episodesFiltre=[]
    window.episodes.forEach(function(e){
        if(verif(e)){
            window.episodesFiltre.push(e)
        }
    })
   
    modif(0,10,1)
    
    
     
})
$('#ajout').click(function(){
    console.log(document.getElementById('ajout'))
    a=document.createElement('a')
    a.setAttribute('class','btn btn-primary')
    a.href="test?input1=test"
    a.innerHTML="test"
    document.getElementById('ajout').appendChild(a)
    
})
$('#titre').keyup(function(e){
    
    target=e.target.value
   
    window.nom=target
    
    window.episodesFiltre=[]
    window.episodes.forEach(function(e){
        if(verif(e)){
            window.episodesFiltre.push(e)
        }
    })
    
    modif(0,10,1)
    
    

})
$('#dateStart').change(function(e){
    
    target=e.target.value
    subDate=target.split('-')
    window.date=subDate[2]+'/'+subDate[1]+'/'+subDate[0]
    
    window.episodes.forEach(function(e){
        verif(e,window.episodesFiltre)
    })
    modif(0,10,1)
    
    
    
    

})

$('#sort_nom').click(function(){
   
    
    if(this.innerHTML=="Default"){
        trie('nom',false)
        this.innerHTML="A...Z"
    }
    else if(this.innerHTML=="A...Z"){
        trie('nom',true)
        this.innerHTML="Z...A"
    }
    else{
        window.episodesFiltre=[]
        window.episodes.forEach(function(e){
            if(verif(e)){
                window.episodesFiltre.push(e)
            }
        })
        modif(0,10,1)
    }
    
})

