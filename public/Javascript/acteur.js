nom=""
prenom=""
buttonsRow=""

function modifier(id, nom, prenom){
        
    if(id!=null){
        document.getElementById('acteur_form_nom').setAttribute('value', nom);
        document.getElementById('acteur_form_prenom').setAttribute('value', prenom);
        document.getElementById('ID').setAttribute('value',id);
        document.getElementById('exampleModalLongTitle').innerHTML="Modification de l'acteur "
                  
    }
    else{
        document.getElementById('acteur_form_nom').setAttribute('value', '');
        document.getElementById('acteur_form_prenom').setAttribute('value', '');
        document.getElementById('ID').setAttribute('value','');  
        document.getElementById('exampleModalLongTitle').innerHTML="Ajouter un acteur"     
    }
    
}


function supprimer(Id){
    if(Id==null){
        
        document.getElementById('form').action='/supprimer_acteurs';
    }
    else{
      
        document.getElementById('form').action='/supprimer_acteur/'+Id;
    }
}
function exporter(){
    document.getElementById('pModalAutre').innerHTML="Exporter ces acteurs";
    document.getElementById('supModalLongTitle').innerHTML="Exportation"
    document.getElementById('submitAutre').setAttribute('class','btn btn-primary');
    document.getElementById('form').action='/export';
    
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

function verif(e){
    bool=true
    
    if(window.prenom!=""){
        bool=bool && e['prenom'].includes(window.prenom)
    }
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
        
            
            
        addCol(e,row,window.ListeFitre,min,max)


        colButton=window.buttonsRow.cloneNode(true)
        colButton.children[0].setAttribute('onclick','modifier("'+e['id']+'","'+e['nom']+'","'+e['prenom']+'")')
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
                boolChekAll=boolChekAll && check.checked==true

                th.appendChild(check)
                row.appendChild(th)
            }
            else{
                row.children[2].setAttribute('colspan','2')
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