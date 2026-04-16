<?php 

include('_core/_includes/config.php'); 

// Globais
global $rootpath;
global $httprotocol;
global $simple_url;

$gowww = $httprotocol.$simple_url;

// Paths reservados do sistema (não são lojas)
$reserved_paths = array(
  'administracao', 'painel', 'afiliado', 'login', 'logout',
  'esqueci', 'novasenha', 'localizacao', '_core', 'api',
  'comece', 'conheca', 'conheca2', 'gerador', 'app',
  'vendor', 'cron.php', '404.php', 'consulta.php', 'mp.php'
);

// Extrair path da URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = ltrim($uri, '/');
$segments = array_values(array_filter(explode('/', $uri)));

$insubdominio = "";
$inrouter_value = "";

// Primeiro segmento do path pode ser um slug de loja/cidade
if( count($segments) > 0 && !in_array($segments[0], $reserved_paths) && $segments[0] != "" ) {
  $possible_slug = mysqli_real_escape_string($db_con, $segments[0]);

  // Verifica se é estabelecimento, cidade ou subdomínio customizado
  $is_store = mysqli_num_rows(mysqli_query($db_con, "SELECT id FROM estabelecimentos WHERE subdominio = '$possible_slug' AND excluded != '1' LIMIT 1"));
  $is_city = mysqli_num_rows(mysqli_query($db_con, "SELECT id FROM cidades WHERE subdominio = '$possible_slug' LIMIT 1"));
  $is_custom = mysqli_num_rows(mysqli_query($db_con, "SELECT id FROM subdominios WHERE subdominio = '$possible_slug' LIMIT 1"));

  if( $is_store || $is_city || $is_custom ) {
    $insubdominio = $possible_slug;
    // O restante dos segmentos vira o inrouter
    $remaining = array_slice($segments, 1);
    $inrouter_value = implode('/', $remaining);
  }
}

// Se não achou loja pelo path, tenta pelo subdomínio do host (compatibilidade)
if( !$insubdominio ) {
  $insubdominio = $_GET['insubdominio'];
  if( !$insubdominio ) {
    $firstdomain = explode(".", $simple_url);
    $firstdomain = $firstdomain[0];
    $host_sub = array_shift((explode('.', $_SERVER['HTTP_HOST'])));
    if( $host_sub != $firstdomain && $host_sub != "www" && $host_sub != "pedeue" && $host_sub != "pedeue-sistema-completo" ) {
      $insubdominio = $host_sub;
    }
  }
  if( $insubdominio ) {
    $inrouter_value = $_GET['inrouter'] ?: $uri;
  }
}

// Busca no banco
$has_insubdominio = "";
$insubdominioid = "";
$insubdominiotipo = "";

if( $insubdominio ) {
  $insubdominio = mysqli_real_escape_string($db_con, $insubdominio);

  // Estabelecimento
  if( mysqli_num_rows( mysqli_query( $db_con, "SELECT id,subdominio FROM estabelecimentos WHERE subdominio = '$insubdominio' AND excluded != '1' LIMIT 1" ) ) ) {
    $query = mysqli_query( $db_con, "SELECT id,subdominio FROM estabelecimentos WHERE subdominio = '$insubdominio' LIMIT 1" );
    $data = mysqli_fetch_array( $query );
    $has_insubdominio = "1";
    $insubdominioid = $data['id'];
    $insubdominiotipo = "1";
  }

  // Cidade
  if( !$has_insubdominio && mysqli_num_rows( mysqli_query( $db_con, "SELECT id,subdominio FROM cidades WHERE subdominio = '$insubdominio' LIMIT 1" ) ) ) {
    $query = mysqli_query( $db_con, "SELECT id,subdominio FROM cidades WHERE subdominio = '$insubdominio' LIMIT 1" );
    $data = mysqli_fetch_array( $query );
    $has_insubdominio = "1";
    $insubdominioid = $data['id'];
    $insubdominiotipo = "2";
  }

  // Subdomínio customizado
  if( !$has_insubdominio && mysqli_num_rows( mysqli_query( $db_con, "SELECT * FROM subdominios WHERE subdominio = '$insubdominio' LIMIT 1" ) ) ) {
    $query = mysqli_query( $db_con, "SELECT * FROM subdominios WHERE subdominio = '$insubdominio' LIMIT 1" );
    $data = mysqli_fetch_array( $query );
    $has_insubdominio = "1";
    $insubdominioid = $data['rel_id'];
    $insubdominiotipo = $data['tipo'];
    if( $insubdominiotipo == "1" ) {
      if( data_info( "estabelecimentos",$insubdominioid,"excluded" ) == "1" ) {
        $has_insubdominio = "0";
        $insubdominioid = "";
        $insubdominiotipo = "";
      }
    }
  }
}

// Se existe o slug
if( $has_insubdominio ) {

    $insubdominiourl = $insubdominio;

    // Roteando
    $router = explode("/", $inrouter_value);
    $inacao = $router[0];
    $inparametro = $router[1];
    $_GET['inrouter'] = $inrouter_value;

    // Estabelecimento
    if( $insubdominiotipo == "1" ) {

      $virtualpath = $rootpath."/app/estabelecimento";

      if( !$inacao ) {
        $chamar = $virtualpath."/index.php";
      }

      if( $inacao == "categoria" ) {
        $chamar = $virtualpath."/categoria.php";
      }

      if( $inacao == "produto" ) {
        $chamar = $virtualpath."/produto.php";
      }

      if( $inacao == "sacola" ) {
        $chamar = $virtualpath."/sacola.php";
      }

      if( $inacao == "pedido" ) {
        $chamar = $virtualpath."/pedido.php";
      }
      
      if( $inacao == "pedido_delivery" ) {
        $chamar = $virtualpath."/pedido_delivery.php";
      }
      
      if( $inacao == "pedido_balcao" ) {
        $chamar = $virtualpath."/pedido_balcao.php";
      }

      if( $inacao == "pedido_mesa" ) {
        $chamar = $virtualpath."/pedido_mesa.php";
      }
      
      if( $inacao == "pedido_outros" ) {
        $chamar = $virtualpath."/pedido_outros.php";
      }
      
      if( $inacao == "pedidosabertos" ) {
        $chamar = $virtualpath."/pedidosabertos.php";
      }
      
      if( $inacao == "pedidosfechados" ) {
        $chamar = $virtualpath."/pedidosfechados.php";
      }

      if( $inacao == "desativado" ) {
        $chamar = $virtualpath."/desativado.php";
      }

      if( $inacao == "fechado" ) {
        $chamar = $virtualpath."/fechado.php";
      }

      if( $inacao == "obrigado" ) {
        $chamar = $virtualpath."/obrigado.php";
      }
	  
	    // Gateways

      if( $inacao == "pix" ) {
        $chamar = $virtualpath."/pix/pix.php";
      }
      
      if( $inacao == "mercadopago" ) {
        $chamar = $virtualpath."/mercadopago/mercadopago.php";
      }

      if( $inacao == "mercadopago_process" ) {
        $chamar = $virtualpath."/mercadopago/mercadopago_process.php";
      }

      if( $inacao == "mercadopago_status" ) {
        $chamar = $virtualpath."/mercadopago/mercadopago_status.php";
      }

      if( $inacao == "pagseguro" ) {
        $chamar = $virtualpath."/pagseguro/pagseguro.php";
      }

      if( $inacao == "pagseguro_id" ) {
        $chamar = $virtualpath."/pagseguro/pagseguro_id.php";
      }

      if( $inacao == "pagseguro_process" ) {
        $chamar = $virtualpath."/pagseguro/pagseguro_process.php";
      }
      if( $inacao == "pagseguro_create_payment" ) {
        $chamar = $virtualpath."/pagseguro/pagseguro_create_payment.php";
      }
      if( $inacao == "pagseguro_status" ) {
        $chamar = $virtualpath."/pagseguro/pagseguro_status.php";
      }

      if( $inacao == "getnet" ) {
        $chamar = $virtualpath."/getnet/getnet.php";
      }

      if( $inacao == "getnet_process" ) {
        $chamar = $virtualpath."/getnet/getnet_process.php";
      }

      if( $inacao == "getnet_status" ) {
        $chamar = $virtualpath."/getnet/getnet_status.php";
      }

      // PWA & integrações

      if( $inacao == "manifest.webmanifest" ) {
        $chamar = $virtualpath."/_layout/manifest.php";
      }

      if( $inacao == "favicon.png" ) {
        $chamar = $virtualpath."/_layout/favicon.php";
      }

      if( $inacao == "serviceworker.js" ) {
        $chamar = $virtualpath."/js/serviceworker.php";
      }

      if( $inacao == "addtohome.js" ) {
        $chamar = $virtualpath."/js/addtohome.js";
      }

      if( $inacao == "index.html" ) {
        $chamar = $virtualpath."/index.php";
      }

      if( $inacao == "shopping.xml" ) {
        $chamar = $virtualpath."/integracao/shopping.php";
      }

      if( $inacao != "index.html" && $inacao != "serviceworker.js" && $inacao != "shopping.xml" && $inacao != "pix" && $inacao != "addtohome.js" && $inacao != "manifest.webmanifest" && $inacao != "favicon.png" && $inacao != "categoria" && $inacao != "produto" && $inacao != "sacola" && $inacao != "pedido" && $inacao != "pedido_delivery" && $inacao != "pedido_balcao" && $inacao != "pedido_mesa"  && $inacao != "pedido_outros" && $inacao != "pedidosabertos" && $inacao != "pedidosfechados" && $inacao != "desativado" && $inacao != "fechado" && $inacao != "obrigado" && $inacao != "" && $inacao != "mercadopago" && $inacao != "pagseguro" && $inacao != "getnet" && $inacao != "getnet_process" && $inacao != "getnet_status" && $inacao != "pagseguro_id" && $inacao != "pagseguro_process" && $inacao != "pagseguro_create_payment" && $inacao != "pagseguro_status" && $inacao != "mercadopago_process" && $inacao != "mercadopago_status") {
        $chamar = $virtualpath."/404.php";
      }

      include($chamar);

    }

    // Cidade
    if( $insubdominiotipo == "2" ) {

      $virtualpath = $rootpath."/app/cidade";

      if( !$inacao ) {
        $chamar = $virtualpath."/index.php";
      }

      if( $inacao == "produtos" ) {
        $chamar = $virtualpath."/produtos.php";
      }

      if( $inacao == "estabelecimentos" ) {
        $chamar = $virtualpath."/estabelecimentos.php";
      }

      if( $inacao == "sacola" ) {
        $chamar = $virtualpath."/sacola.php";
      }

      if( $inacao != "estabelecimentos" && $inacao != "produtos" && $inacao != "sacola" && $inacao != "" ) {
        $chamar = $virtualpath."/404.php";
      }

      include($chamar);

    }

} else {

    if( $insubdominio ) {
      include("404.php");
    } else {
      if( !$uri || $uri == "index.php" ) {
        header("Location: ".$httprotocol.$simple_url."/localizacao");
      }
    }

}

?>
