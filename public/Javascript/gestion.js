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
function addCol(test,val,row){
    
    
    if(test){
        val.forEach(function(e){
            newCol=document.createElement('th')
            newCol.style="text-align: center; vertical-align: middle;"
            newCol.innerHTML=e
            row.appendChild(newCol)
        })
    }
    
}
function pager(){
    
    if(window.episodesFiltre.length==0){
        tempListe=episodes
    }
    else{
        tempListe=episodesFiltre
    }
    nbPage=Math.floor(tempListe.length/10)
    
    pages=document.getElementById('pages')
    prev=document.createElement('li')
    prev.setAttribute('class',"page-item disabled")
    min=0
    max=10
    buttonPrev=document.createElement('span')
    buttonPrev.setAttribute('class',"page-link")
    buttonPrev.setAttribute('id',"prev")
    buttonPrev.type="button"
    buttonPrev.setAttribute('onclick','modif(0,10)')
    buttonPrev.innerHTML="&laquo;&nbsp;Previous"
    prev.appendChild(buttonPrev)
    pages.appendChild(prev)
    
    
    for(i=0; i<=nbPage;i++){
        
        page=document.createElement('li')
        page.setAttribute('class',"page-item")
        buttonPage=document.createElement('span')
        buttonPage.setAttribute('class',"page-link")
        buttonPage.setAttribute('onclick','modif('+min+','+max+')')
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
    buttonNext.setAttribute('onclick','modif('+(min-10)+','+(max-10)+')')
    buttonNext.innerHTML="Next&nbsp;&raquo;"

    next.appendChild(buttonNext)
    pages.appendChild(next)
    
}
function verif(e){
    bool=true
    if(window.nom!==""){
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
    
    Array.from(document.getElementById('pages').childNodes).forEach(function(e){
        //console.log(e)
        e.remove()
    })
    deleteRow()
    
    if(window.episodesFiltre.length==0 || window.GlobalTarget=="vide" || window.nom=="" || window.date==""){
        tempListe=episodes
    }
    else{
        tempListe=window.episodesFiltre
    }
    window.episodesFiltre=[]
        
    tempListe.forEach(function(e){
        
        row=document.createElement('tr')
        splitDate=e['date'].date.substring(0,10).split('-')
        temp=splitDate[2]+"/"+splitDate[1]+"/"+splitDate[0]
            
            
        addCol(verif(e),[e['nom'],temp,e['serieNom']],row)
            
            
                
            
            
            
        if(row.children['length']>=1){
            if(min!=null){
                if(tempListe.indexOf(e)>=min && tempListe.indexOf(e)<max){
                    window.tbody.appendChild(row)
                }
                    
            }
            else{
                window.tbody.appendChild(row)
            }
                
                
               
            window.episodesFiltre.push(e)
        }
            
            
    })
    pager()
    
        
        
}

function modif(min,max){
    filtre(min,max)
    if(window.episodesFiltre.length==0){
        filtre(min,max)
    }
    //console.log(document.getElementById("prev"))
    if(min!=0 && max!=10){
        document.getElementById("prev").parentElement.setAttribute('class','page-item')
        document.getElementById("prev").setAttribute('onclick','modif('+(min-10)+','+(max-10)+")")
    }
    else{
        document.getElementById("prev").parentElement.setAttribute('class','page-item disabled')
    }
    if(max==(10*Math.ceil(window.episodesFiltre.length/10))){
        document.getElementById("next").parentElement.setAttribute('class','page-item disabled')
    }
    else{
        document.getElementById("next").parentElement.setAttribute('class','page-item')
        document.getElementById("next").setAttribute('onclick','modif('+(min+10)+','+(max+10)+")")
    }
    
    
}

$('#ep').change(function(e){
    target=e.target.value
    window.GlobalTarget=target
    
   
    filtre()
    if(window.episodesFiltre.length==0){
        filtre()
    }
    
     
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
    
    
    
    filtre()
    if(window.episodesFiltre.length==0){
        filtre()
    }
    

})
$('#dateStart').change(function(e){
    
    target=e.target.value
    subDate=target.split('-')
    window.date=subDate[2]+'/'+subDate[1]+'/'+subDate[0]
    
    
    filtre()
    if(window.episodesFiltre.length==0){
        filtre()
    }
    
    
    

})

