buttonsRow=""
dateStart=""
dateEnd=""
nom=""
saison=""
episodes=""

function verif(e){
    bool=true
    
    if(window.nom!=""){
    bool=bool && e['nom'].includes(window.nom)
    }
   
    if(window.dateStart!=="" && window.dateEnd!==''){
        temp=new Date(e['date']['date'])
        bool=bool && temp.getTime()>window.dateStart.getTime() && temp.getTime()<window.dateEnd.getTime()
        
        
    }

    if(window.saison!==''){
        bool=bool && e['saison']>=window.saison
    }
    if(window.episodes!==''){
        bool=bool && e['episodes']>=window.episodes
    }

    return bool
    
    
}


function filtre(min,max,mod){   
    deleteRow()    
    window.ListeFitre.forEach(function(e){
        
        row=document.createElement('tr')
        splitDate=e['date'].date.substring(0,10).split('-')
        temp=splitDate[2]+"/"+splitDate[1]+"/"+splitDate[0]
            
            
        addCol(e,row,window.ListeFitre,min,max)
        colButton=window.buttonsRow.cloneNode(true)
       
        
        
        colButton.children[0].setAttribute('onclick','modifier("'+e['nom']+'","'+e['date']['date'].substring(0,10)+'","'+e['resume']+'","'+e['Ba']+'","'+e['affiche']+'","'+e['id']+'")')
        colButton.children[0].setAttribute('id','modif_"'+e['id'])
        colButton.children[0].setAttribute('name','modif_"'+e['id'])

        colButton.children[1].setAttribute('onclick','supprimer("'+e['id']+'")')
        colButton.children[1].setAttribute('id','sup_"'+e['id'])
        colButton.children[1].setAttribute('name','sup_"'+e['id'])
        colButton.children[2].href="gerer_episode/"+e["id"]
        console.log(colButton)

        row.appendChild(colButton)
        if(window.boolExport){
            th=document.createElement('td')
            th.style="text-align: center; vertical-align: middle;"
            check=document.createElement('input')
            check.type='checkbox'
            check.setAttribute('id',e['id'])
            check.setAttribute('name',e['id'])
            check.setAttribute('onclick','addExport("'+e['id']+'")')
            console.log(window.listeExport,e['id'].toString())

            if(window.listeExport.includes(e['id'].toString())){
                check.checked=true
            }
            th.appendChild(check)
            row.appendChild(th)
        }
        else{
            row.children[4].setAttribute('colspan','2')
        }
        
        if(row.children['length']>0){
            window.tbody.appendChild(row)
        }
        
    })
    if(window.boolExport==false){
        pager(window.ListeFitre)
    }
    
    
        
        
}

function modifier(nom, dateDiff, resume, url, affiche, id){
        
    if(id!=null){
        document.getElementById('serie_form_nom').setAttribute('value', nom);
        document.getElementById('serie_form_date_diff').setAttribute('value', dateDiff);
        document.getElementById('serie_form_resume').value=resume;
        document.getElementById('serie_form_url_ba').setAttribute('value', url);
        document.getElementById('image').setAttribute('src','/photo/'+affiche);
        document.getElementById('ID').setAttribute('value',id);
        document.getElementById('exampleModalLongTitle').innerHTML="Modification de la série" ;  
        
    }
    else{
        document.getElementById('serie_form_photo').setAttribute('value','');
        document.getElementById('serie_form_nom').setAttribute('value','');
        document.getElementById('serie_form_date_diff').setAttribute('value','');
        document.getElementById('serie_form_resume').value='';
        document.getElementById('serie_form_url_ba').setAttribute('value','');
        document.getElementById('image').setAttribute('src','');
        document.getElementById('ID').setAttribute('value','');
        document.getElementById('exampleModalLongTitle').innerHTML="Ajouter une série" ;
        
    }
    
}


function supprimer(Id){
    document.getElementById('submitAutre').setAttribute('class','btn btn-danger');
    if(Id==null){
        document.getElementById('form').action='/supprimer_series';
    }
    else{
        
        document.getElementById('form').action='/supprimer_serie/'+Id;
    }
    
}
function exporter(){
    
    document.getElementById('submitAutre').setAttribute('class','btn btn-primary');
    document.getElementById('pModalAutre').innerHTML="Exporter ces séries";
    document.getElementById('supModalLongTitle').innerHTML="Exportation"
   
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

function previewPicture(e){
    var image = document.getElementById('image');
    
    const [picture] = e.files;
    if (picture) {
        image.src = URL.createObjectURL(picture);
    } 
}



function addPerso(tag, num){
    
    
    
    
    if(tag=="multiple"){
        p=document.getElementById('persoTab_0_0').cloneNode(true)
        balise=document.getElementById('collapsePerso_'+num).children[0]
        nbPerso=document.getElementById('nbPerso_'+num)
        numero=balise.children['length']

        p.setAttribute('id','persoTab_'+num+"_"+numero)

        select=p.children[0]
        
        select.setAttribute('id','acteur_'+num+"_"+numero)
        select.setAttribute('name','acteur_'+num+"_"+numero)

        input=p.children[1]
        input.setAttribute('id','persoNom_'+num+"_"+numero)
        input.setAttribute('name','persoNom_'+num+"_"+numero)
        
        
    }
    else{
        balise=document.getElementById('perso')
        p=document.getElementById('perso_0').cloneNode(true)
        numero=balise.children['length']-1
        
        p.setAttribute('id','perso_'+numero)
        p.children[0].setAttribute('id','acteur_'+numero)
        p.children[0].setAttribute('name','acteur_'+numero)
        p.children[1].setAttribute('id','persoNom_'+numero)
        p.children[1].setAttribute('name','persoNom_'+numero)
    }
    
    balise.appendChild(p)
}




function supprimerPerso(tag,num){
    
    bool=true
    if(tag=='multiple'){
        balise=document.getElementById('collapsePerso_'+num)
        nbPerso=document.getElementById('nbPerso_'+num)
        numero=balise.children[0].children['length']
        perso=balise.children[0].lastElementChild
        if(numero-1==0){
            nbPerso.setAttribute('value',1)
        }
        else{
            nbPerso.setAttribute('value',numero-1)
        }
        
        if(perso.getAttribute('id')=='persoTab_'+num+"_0"){
            bool=false
            
        }
        
    }
    else{
        balise=document.getElementById('perso')
        perso=balise.lastElementChild
        console.log(perso.getAttribute('id'))
        if(perso.getAttribute('id')=="perso_0"){
            bool=false  
        }
       
    }
    
    if(bool){
        perso.remove()
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
    console.log(document.getElementById(id))
    if(document.getElementById(id).checked){
        window.listeExport.push(id)
    }
    else{
        index=window.listeExport.indexOf(id)
        window.listeExport.splice(index,1)
    }
}




