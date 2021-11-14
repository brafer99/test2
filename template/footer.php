</div>
    </div>
    <script type="text/javascript">
        var boton = document.getElementById('boton');
        var input = document.getElementById('contraseña');

        boton.addEventListener('click',mostrarContraseña);

        function mostrarContraseña(){
            if(input.type == "password"){
                input.type = "text";
            }else{
                input.type = "password";
            }
        }
    </script>

</body>
</html>