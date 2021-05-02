function AsignaValor(Nombre,Valor){
    var Campo = document.getElementById(Nombre);
    if(Valor=="Borrar"){
        Campo.value = Campo.value.substring(0, Campo.value.length - 1);
    }else{
        if (Valor == "Limpiar"){
            Campo.value = "";
        }else{
            if(Campo.value!=""){
                Campo.value = Campo.value + Valor;
            }else{
                Campo.value = Valor;
            }
        }
    }
}

function marcador(Div,Nombre){
    var resultado = "";
    var num  = new Array('1','2','3','4','5','6','7','8','9','0');
    var key  = new Array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',' ');
    resultado = "<table cellpadding='1' cellspacing='1' width='100'>";
    var ini = 0;
    var fin = 10;
    resultado += "<tr bgcolor='#F0F7FD'>";
    for ( var n=0; n<10; ++n ){
        resultado += "<td align='center'><input type='button' onclick=\"AsignaValor('"+Nombre+"','"+num[n]+"')\" value=" + num[n] + " class='button_key'></td>";
    }
    resultado += "</tr>";
    for ( var i=0; i<3; ++i ){
        resultado += "<tr bgcolor='#F0F7FD'>";
        for ( var j=ini; j<fin; ++j ){
            if (key[j] == " "){
                resultado += "<td align='center'><input type='button' onclick=\"AsignaValor('"+Nombre+"','"+key[j]+"')\" value=' ' class='button_key'></td>";
            }else{
                resultado += "<td align='center'><input type='button' onclick=\"AsignaValor('"+Nombre+"','"+key[j]+"')\" value=" + key[j] + " class='button_key'></td>";
            }
        }
        if(j<20){
            ini = j;
            fin = ini + 10;
        }else if(j==20){
            ini = j;
            fin = ini + 7;
        }else if(j==27){
            resultado += "<td align='center'><input type='button' onclick=\"AsignaValor('"+Nombre+"','Borrar')\" value='<-' class='button_key'></td>";
            resultado += "<td bgcolor='#1C5280' colspan='4' align='center' style='cursor:pointer;' onclick=\"AsignaValor('"+Nombre+"','Limpiar')\" class='Button'><strong style='color: white';>Limpiar</strong></td>";
        }
        resultado += "</tr>";
    }
    resultado += "</table><br>";
    document.getElementById(Div).innerHTML=resultado;
}