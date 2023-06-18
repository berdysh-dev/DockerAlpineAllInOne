<?php
    class CL {

        function __construct() {
$this->HEAD=<<<'EOF'
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Language" content="ja">
<style>
@font-face {
    font-family: 'Programma';
    font-weight: bold;
    src: url('Programma-Bold.woff2') format('woff2');
}
body {
    background-color: linen;
    padding-left: 5%;
    padding-right: 5%;
}
ul {
    list-style: none;
}
ul ul {
    list-style: none;
}
hr {
    background-color: black;
    border: 0px;
    color: black;
    height: 2px;
    padding: 0px;
}
div#box {
    background-color: white;
    border: 2pt solid black;
}
div#grammar {
    padding-top: 1em;
}
div#grammar a:visited {
    color: black;
}
div#spec {
    display: grid;
    gap: 1em;
    grid-template-columns: 1fr max-content;
}
div#text {

}
p.rule {
    font-family: "Open Sans", Futura, "Gill Sans", Arial, Helvetica, sans-serif;
    font-weight: normal;
    margin-left: 4em;
    margin-right: 1em;
    page-break-inside: avoid;
    text-indent: -2em;
    white-space: pre-line;
}
code, pre, tt  {
    font-family: Programma, monospace;
    font-size: 100%;
    font-style: normal;
    font-weight: bold;
}
small {
    font-family: 'Century Schoolbook', serif;
    font-size: 8pt;
    font-style: italic;
    font-weight: normal;
    margin-left: 2pt;
    margin-right: 0.75pt;
}
a {
    text-decoration: none;
}
a:link {
    color: navy;
}
a:visited {
    color: maroon;
}
a:hover {
    color: blue;
    text-decoration: underline;
}
a:active {
    color: red;
}
</style>
</style>
</head><body>
EOF;

$this->TAIL=<<<'EOF'
</body>
</html>
EOF;
        }

        function phpinfo(){
            phpinfo() ;
        }

        function base64(){
            echo $this->HEAD ;
            echo 'BASE64 Decoder<hr>' ;
            echo '<textarea cols="100" rows="30"></textarea>' ;
            echo $this->TAIL ;
        }

        function uuid_gen(){
            header('Content-Type: text/plain') ;
            echo uuid_create(UUID_TYPE_RANDOM) ;
        }

        function Menu(){
            echo $this->HEAD ;
            echo "Menu" ;
            echo "<hr>" ;

            printf('<a href="%s">Top</a><br>','/') ;
            printf('<a href="%s">phpinfo</a><br>','/phpinfo/') ;
            printf('<a href="%s">uuid</a><br>','/uuid/') ;
            printf('<a href="%s">base64</a><br>','/base64/') ;
            echo $this->TAIL ;
        }

        function Top(){
            echo $this->HEAD ;
            echo 'BerdyshDev' ;
            echo '<hr>' ;

            printf('<a href="%s">GitHub</a><br>','https://github.com/berdysh-dev') ;
            printf('<a href="%s">Composer</a><br>','https://packagist.org/packages/berdysh-dev/') ;
            printf('<a href="%s">DockerHub</a><br>','https://hub.docker.com/repositories/berdyshdev2') ;

            echo '<hr>' ;

            if(0){
                echo '<pre>' ;
                print_r($_SERVER);
                echo '</pre>' ;
            }

            printf('REMOTE_ADDR[%s]<br>',$_SERVER['REMOTE_ADDR']) ;
            printf('USER_AGENT[%s]<br>',$_SERVER['HTTP_USER_AGENT']) ;
            echo $this->TAIL ;
        }

        function Entry(){

            $this->PATH = $_SERVER['SCRIPT_NAME'] ;

            switch($this->PATH){
            case '/phpinfo/': $this->phpinfo() ; break ;
            case '/uuid/': $this->uuid_gen() ; break ;
            case '/base64/': $this->base64() ; break ;
            case '/menu/': $this->Menu() ; break ;
            default: $this->Top() ; break ;
            }

        }
    }

    if($ctx = new CL()){ $ctx->Entry() ; }


