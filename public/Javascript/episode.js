buttonsRow=""
dateStart=""
dateEnd=""
nom=""
saison=""
serie=""

function modifier(id, nom, dateDiff, resume, index, saison){
        
    selectBase=document.getElementById('serie')
    if(id!=null){
        document.getElementById('episode_form_nom').setAttribute('value', nom);
        document.getElementById('episode_form_date_prem_diff').setAttribute('value', dateDiff);
        document.getElementById('episode_form_resume').value= resume;
        document.getElementById('saison').value=saison;
        document.getElementById('ID').setAttribute('value',id);
        document.getElementById('exampleModalLongTitle').innerHTML="Modification de l'épisode"
        
       
        
        
        
        if(selectBase!=null){
            Array.from(selectBase.options).forEach(function(e){
               
                if(e.getAttribute('id')==index ){
                   
                    e.setAttribute("selected",'selected')
                }
                else if($( this ).prop( "selected" )){
                   e.removeAttr("selected")
                }
            })
            
        }
                  
    }
    else{
        
        document.getElementById('episode_form_nom').setAttribute('value','');
        document.getElementById('episode_form_date_prem_diff').setAttribute('value', '');
        document.getElementById('episode_form_resume').value= '';
        document.getElementById('ID').setAttribute('value','');
        document.getElementById('exampleModalLongTitle').innerHTML="Ajout d'un épisode"
        
        if(selectBase!=null){
            Array.from(selectBase.options).forEach(function(e){
                 if(e.getAttribute('value')=="" ){
                    e.setAttribute("selected",'selected')
                }
                else if($( this ).prop( "selected" )){
                    e.removeAttr("selected")
                }
            })
           
        }
        
        
    }
    
}


function supprimer(Id){
    if(Id==null){
     
        document.getElementById('form').action='/supprimer_episodes';
    }
    else{
      
        document.getElementById('form').action='/supprimer_episode/'+Id;
    }
}

function exporter(){
    
    document.getElementById('submitAutre').setAttribute('class','btn btn-primary');
    document.getElementById('pModalAutre').innerHTML="Exporter ces séries";
    document.getElementById('supModalLongTitle').innerHTML="Exportation"
   
    document.getElementById('form').action='/export?type=episode'
    
    
}

function verif(e){
    bool=true
    
    if(window.nom!=""){
    bool=bool && e['nom'].includes(window.nom)
    }
   
    if(window.dateStart!=="" && window.dateEnd!==''){
        temp=new Date(e['date']['date'])
        bool=bool && temp.getTime()>window.dateStart.getTime() && temp.getTime()<window.dateEnd.getTime()
        
        
    }
    if(window.serie!==''){
        bool=bool && e['serieId']==window.serie
    }
    if(window.saison!==''){
        bool=bool && e['saison']==window.saison
    }
    
    return bool
    
    
}

function filtre(min,max){
    
    boolChekAll=true
    deleteRow()    
    window.ListeFitre.forEach(function(e){
        
        row=document.createElement('tr')
        splitDate=e['date'].date.substring(0,10).split('-')
        temp=splitDate[2]+"/"+splitDate[1]+"/"+splitDate[0]
            
            
        addCol(e,row,window.ListeFitre,min,max)


        colButton=window.buttonsRow.cloneNode(true)
        colButton.children[0].setAttribute('onclick','modifier("'+e['id']+'","'+e['nom']+'","'+e['date']['date'].substring(0,10)+'","'+e['resume']+'","'+e['serieId']+'","'+e['saison']+'")')
        colButton.children[0].setAttribute('id','modif_"'+e['id'])
        colButton.children[0].setAttribute('name','modif_"'+e['id'])
        colButton.children[1].setAttribute('onclick','supprimer("'+e['id']+'")')
        colButton.children[1].setAttribute('id','sup_"'+e['id'])
        colButton.children[1].setAttribute('name','sup_"'+e['id'])
        colButton.children[1].setAttribute('href','gerer_episode/'+e['id'])

        row.appendChild(colButton)
       
       
            
        
        
        if(row.children['length']>1){
            if(window.boolExport){
                th=document.createElement('td')
                th.style="text-align: center; vertical-align: middle;"
                check=document.createElement('input')
                check.type='checkbox'
                check.setAttribute('id',e['id'])
                check.setAttribute('name',e['id'])
                check.setAttribute('onclick','addExport("'+e['id']+'")')
                
    
                if(window.listeExport.includes(e['id'].toString())){
                    check.checked=true
                }
                //console.log(check)
                boolChekAll=boolChekAll && check.checked==true

                th.appendChild(check)
                row.appendChild(th)
            }
            else{
                row.children[4].setAttribute('colspan','2')
            }
            
           
            window.tbody.appendChild(row)
           
        }
        
    })
    
    if(window.boolExport==false){
        
        pager(window.ListeFitre)
    }
    else{
        Array.from(window.tbody.children).forEach(function(e){
            boolChekAll=boolChekAll && e.children[5].children[0].checked==true
        })
        
        if(boolChekAll){
            document.getElementById('checkall').checked=true
        }
        else{
            document.getElementById('checkall').checked=false
        }
    }
    
    
        
        
}

function modExport(){
    
    if(document.getElementById("checkExport").checked){
        window.boolExport=true
        document.getElementById("checkall").disabled = false
        
    }
    else{
        window.boolExport=false
        document.getElementById("checkall").disabled = true

    }
    modif(0,10,1,window.ListeBase)
}
function addExport(id){
   
    if(document.getElementById(id).checked){
        window.listeExport.push(id)
    }
    else{
        index=window.listeExport.indexOf(id)
        window.listeExport.splice(index,1)
    }
}