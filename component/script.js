<script type="text/javascript">	
		function checkRegistration(){
			var nome = document.formreg.nome.value;
			var cog = document.formreg.cog.value;
			var nascita = document.formreg.nascita.value;
			var email = document.formreg.mail.value;
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			var pass = document.formreg.psw.value;
			var passconf = document.formreg.pswconf.value;
			var ok=true;
			if ((nome == "") || (nome == "undefined")){
				document.getElementById('nome').innerHTML = '<span class=\"error\">(!)</span> nome';
				ok=false;
			}
			
			else
				document.getElementById('nome').innerHTML = 'nome';
				
			if ((cog == "") || (cog == "undefined")){
				document.getElementById('cog').innerHTML = '<span class=\"error\">(!)</span> cognome';
				ok=false;
			}
			else
				document.getElementById('cog').innerHTML = 'cognome';
				
			if (nascita == "" || nascita =="(aaaa/mm/dd)" ||
				nascita.substring(4,5) != "/" ||
				nascita.substring(7,8) != "/" ||
				isNaN(nascita.substring(0,4)) ||
				isNaN(nascita.substring(5,7)) ||
				isNaN(nascita.substring(8,10))|| 
				nascita.substring(0,4) > 1995 ||
				nascita.substring(5,7) > 12 ||
				nascita.substring(8,10) > 31)  {
				document.getElementById('nascita').innerHTML = '<span class=\"error\">(!)</span> data di nascita';
				ok=false;
			}
			else
				document.getElementById('nascita').innerHTML = 'data di nascita';
				
			if (!filter.test(email)){
				document.getElementById('labmail').innerHTML = '<span class=\"error\">(!)</span> email';
				ok=false;
			}
			else
				document.getElementById('labmail').innerHTML = 'email';
				
			if (pass=="" || pass != passconf){
				document.getElementById('psw').innerHTML = '<span class=\"error\">(!)</span> password';
				ok=false;
			}
			else
				document.getElementById('psw').innerHTML = 'password';
				
			if(ok==true){
				document.formreg.action = "registration.php?cmd=submit";
				document.formreg.submit();
			}		
		}
		
		
</script>