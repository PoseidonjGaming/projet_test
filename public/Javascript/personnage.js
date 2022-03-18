serie=""
buttonsRow=""
nom=""
prenom=""
acteur=""


function modifier(id, nom,index,acteur){
    selectBase=document.getElementById('personnage_form_serie')
    console.log(selectBase.options)
    if(id!=null){
        document.getElementById('personnage_form_nom').setAttribute('value', nom);
        document.getElementById('ID').setAttribute('value',id);
        document.getElementById('exampleModalLongTitle').innerHTML="Modification du personnage" 
        if(selectBase!=null){
            Array.from(selectBase.options).forEach(function(e){
               
                if(e.value==index){
                   
                    e.setAttribute("selected",'selected')
                }
                else if($( this ).prop( "selected" )){
                   e.removeAttr("selected")
                }
            })
            
        }
        Array.from(document.getElementById('personnage_form_acteur').options).forEach(function(e){
               
            if(e.value==acteur){
               
                e.setAttribute("selected",'selected')
            }
            else if($( this ).prop( "selected" )){
               e.removeAttr("selected")
            }
        })
        
                  
    }
    else{
        document.getElementById('personnage_form_nom').setAttribute('value', '');
        document.getElementById('ID').setAttribute('value','');  
        document.getElementById('exampleModalLongTitle').innerHTML="Ajouter un personnage" 
        
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
    
}


function supprimer(Id){
    if(Id==null){
        
        document.getElementById('form').action='/supprimer_personnages';
    }
    else{
        
        document.getElementById('form').action='/supprimer_personnage/'+Id;
        
    }
}
function exporter(){
    document.getElementById('pModalAutre').innerHTML="Exporter ces personnages";
    document.getElementById('supModalLongTitle').innerHTML="Exportation"
    document.getElementById('submitAutre').setAttribute('class','btn btn-primary');
    document.getElementById('form').action='/export'
    input=document.createElement('input')
    input.type="hidden"
    input.setAttribute('id','listeExport')
    input.setAttribute('name','listeExport')
    Listvalue=window.listeExport[0]
    window.listeExport.splice(0,1)
    window.listeExport.forEach(function(e){
        Listvalue=Listvalue+","+e
    })
    input.value=Listvalue
    
    document.getElementById('pModalAutre').appendChild(input)
    
}
function addSelect(cible,nbPerso){ 
    selectBase=document.getElementById(cible)
    select=document.createElement('select')
    select.setAttribute('class',"form-select")
    if(cible=='inputActeur_0'){
        select.setAttribute('id','inputActeur_'+nbPerso)
        select.setAttribute('name','inputActeur_'+nbPerso)  
    }
    else{
        select.setAttribute('id','inputSerie_'+nbPerso)
        select.setAttribute('name','inputSerie_'+nbPerso)  
    }
    
    
    for (i = 0; i < selectBase.options.length; i++) {
        select[select.options.length]=new Option(selectBase.options[i].label,selectBase.options[i].value)
    }
    return select
}
function verif(e){
    bool=true
    
    
    if(window.nom!=""){
        bool=bool && e['nom'].includes(window.nom)
        }
   
    if(window.serie!==''){
        bool=bool && e['serieId']==window.serie
    }
    if(window.acteur!==''){
        bool=bool && e['acteurId']==window.acteur
    }
    
    
    return bool
    
    
}
function filtre(min,max){
    
    boolChekAll=true
    deleteRow()    
    window.ListeFitre.forEach(function(e){
        
        row=document.createElement('tr')
       
            
        addCol(e,row,window.ListeFitre,min,max)


        colButton=window.buttonsRow.cloneNode(true)
        colButton.children[0].setAttribute('onclick','modifier("'+e['id']+'","'+e['nom']+'","'+e['serieId']+'","'+e['acteurId']+'")')
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
                row.children[3].setAttribute('colspan','2')
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