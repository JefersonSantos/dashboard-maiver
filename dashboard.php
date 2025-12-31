<?php
require_once 'auth.php';

// Captura as datas do formulário
$data_inicio = isset($_POST['data_inicio']) ? date('d/m/Y', strtotime($_POST['data_inicio'])) : date('01/m/Y');
$data_fim = isset($_POST['data_fim']) ? date('d/m/Y', strtotime($_POST['data_fim'])) : date('d/m/Y');
$exportar = isset($_POST['exportar']);

// Busca produtos cadastrados na tabela produtos (prioridade)
$produtos_cadastrados = [];
$query_produtos = "SELECT tabela, nome, imagem_url FROM produtos WHERE ativo = 1 ORDER BY nome ASC";
$result_produtos = $conn->query($query_produtos);
if ($result_produtos) {
  while ($row = $result_produtos->fetch_assoc()) {
    $produtos_cadastrados[] = $row['tabela'];
  }
}

// Se não houver produtos cadastrados, busca tabelas _rec e _ap do banco
$tabelas = [];
if (empty($produtos_cadastrados)) {
  $sqlTabelasRec = "SHOW TABLES LIKE '%\\_rec'";
  $sqlTabelasAp = "SHOW TABLES LIKE '%\\_ap'";
  $resTabelasRec = $conn->query($sqlTabelasRec);
  $resTabelasAp = $conn->query($sqlTabelasAp);
  
  if ($resTabelasRec) {
    while ($row = $resTabelasRec->fetch_row()) {
      $tableName = $row[0];
      $base = preg_replace('/_rec$/', '', $tableName);
      $tabelas[] = $base;
    }
  }
  
  if ($resTabelasAp) {
    while ($row = $resTabelasAp->fetch_row()) {
      $tableName = $row[0];
      $base = preg_replace('/_ap$/', '', $tableName);
      $tabelas[] = $base;
    }
  }
  
  $tabelas = array_unique($tabelas);
  sort($tabelas);
} else {
  // Usa produtos cadastrados
  $tabelas = $produtos_cadastrados;
}

$legendas = [
  'DWM' => 'DW Marketing',
  'NTC' => 'Nitro Company',
  'LMN' => 'Luminon',
  'SIP' => 'Super Info Products',
  'GVM' => 'GV Mídias',
  'ADV' => 'Advende',
  'AUD' => 'Audax',
  'IRM' => 'IRM Digital',
  'FNX' => 'Grupo Fênix',
  'WAV' => 'Wave',
  'ISD' => 'Insider',
  'VG '  => 'VG ',
  'V2G' => 'V2 Global',
  'BHV' => 'BHever',
  'LGW' => 'Ligh Weigt',
  'BLC' => 'Black Scale',
  'HPG' => 'HPG Ventures',
  'EEP' => 'E&P Laps',
  'RED' => 'R&D',
  'FMG' => 'Fahto Media Group',
  'DGM' => 'Digmach',
  'ZEN' => 'Z&N Global',
  'AST' => 'Astron',
  'NWM' => 'Nw Media',
  'INF' => 'Influenciei',
  'SAG' => 'Sage',
  'UPX' => 'UPX Caps',
  'DTC' => 'Direct Cash',
  'AMX' => 'Aura Matrix',
  'MVX' => 'MVX Group',
  '4MG' => '4 Maps Group',
  'NEX' => 'Nexis'
];

// Busca produtos do banco de dados (todos os produtos vêm da tabela `produtos`)
$produtos = [];
$query_produtos = "SELECT id, tabela, nome, imagem_url FROM produtos WHERE ativo = 1";
$result_produtos = $conn->query($query_produtos);
if ($result_produtos) {
  while ($row = $result_produtos->fetch_assoc()) {
    $produtos[$row['tabela']] = [
      'id'   => $row['id'],
      'nome' => $row['nome'],
      'img'  => $row['imagem_url']
    ];
  }
}

// Fallback antigo de produtos foi descontinuado.
// Toda configuração de nome e imagem agora é feita exclusivamente via módulo produtos.php.
// Este array é mantido apenas temporariamente para compatibilidade visual, mas não é mais usado.
$produtos_fallback = [
  'adv_bioxcell' => [
      'nome' => 'Bioxcell',
      'img'  => 'https://thumbor.cartpanda.com/nH5ao1dBlpgq_JimR_hYOI47F0Y=/800x0/https://assets.mycartpanda.com/static/products_images/ec/62/60/1746579329.png'
  ],
  'adv_brainxcell' => [
      'nome' => 'BrainXCell',
      'img'  => 'https://thumbor.cartpanda.com/j3YBhcDWQkyAaPpEqEPRiWACbOM=/800x0/https://assets.mycartpanda.com/static/products_images/c0/37/53/1760122667.png'
  ],
  'adv_honeyboostxl' => [
      'nome' => 'HoneyBoost XL',
      'img'  => 'https://thumbor.cartpanda.com/FRAdAtVwQH5ca5-vunog6M6fCyQ=/800x0/https://assets.mycartpanda.com/static/products_images/bf/d4/cd/1760375488.png'
  ],
  'aud_vitalprime' => [
      'nome' => 'Vital Prime',
      'img'  => 'https://thumbor.cartpanda.com/7M5Hy5HbeOaGygFPMCZ60Ng1AE4=/800x0/https://assets.mycartpanda.com/static/products_images/0c/9f/90/1722531657.png'
  ],
  'dwm_nerveguard' => [
      'nome' => 'Nerve Guard Premium',
      'img'  => 'https://thumbor.cartpanda.com/Q2tbi9eGdaplAEPi06doHo4X_AM=/800x0/https://assets.mycartpanda.com/static/products_images/d3/a7/08/1743091257.jpg'
  ],
  'fnx_prostabliss' => [
      'nome' => 'Prostabliss',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'gvm_glycoshizen' => [
      'nome' => 'Glycoshizen',
      'img'  => 'https://maiver.com.br/wp-content/uploads/2025/05/11-1024x1024.png'
  ],
  'irm_lipoboost' => [
      'nome' => 'Lipo Boost',
      'img'  => 'https://thumbor.cartpanda.com/9cViM3FDOGbKxojixpovqw5fYeY=/800x0/https://assets.mycartpanda.com/static/products_images/0e/8f/fe/1752784234.png'
  ],
  'irm_lipopure' => [
      'nome' => 'LipoPure',
      'img'  => 'https://thumbor.cartpanda.com/yuaEtovVOKqWfRrHWJhTluwEc9k=/800x0/https://assets.mycartpanda.com/static/products_images/32/44/f0/1752592375.png'
  ],
  'irm_slimmetrix' => [
      'nome' => 'Slim Metrix',
      'img'  => 'https://thumbor.cartpanda.com/E_M_9FdQrTVB55Euo4Jo0oNJXMs=/800x0/https://assets.mycartpanda.com/static/products_images/d2/7e/4b/1750516423.png'
  ],
  'isd_mounja' => [
      'nome' => 'Mounja',
      'img'  => 'https://thumbor.cartpanda.com/D9NB1DWGoLL80yTnIHIFHMpYMUY=/800x0/https://assets.mycartpanda.com/static/products_images/2c/e5/22/1749490734.png'
  ],
  'isd_cardiocare' => [
      'nome' => 'Cardio Care',
      'img'  => 'https://thumbor.cartpanda.com/uTvyjSbkwVYhIbUGY301tUzly28=/800x0/https://assets.mycartpanda.com/static/products_images/68/be/a9/1757341570.png'
  ],
  'isd_gastricalm' => [
      'nome' => 'Gastri Calm',
      'img'  => 'https://thumbor.cartpanda.com/eigmoqC2INYE5flq_yc42U0VA2Y=/800x0/https://assets.mycartpanda.com/static/products_images/d5/aa/41/1756998182.png'
  ],
  'lgw_divine_script' => [
      'nome' => 'Divine Script',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'lmn_barislend' => [
      'nome' => 'Barislend',
      'img'  => 'https://thumbor.cartpanda.com/fmbnRdgxlboMfZOKY_le1nm9BDI=/800x0/https://assets.mycartpanda.com/static/products_images/63/ac/26/1748894982.png'
  ],
  'ntc_glucoforce' => [
      'nome' => 'Gluco Force',
      'img'  => 'https://thumbor.cartpanda.com/ert0b2y7heiG0_BrbwNOGuqnTv4=/800x0/https://assets.mycartpanda.com/static/products_images/15/c0/31/1743439268.png'
  ],
  'ntc_prostashield' => [
      'nome' => 'Prosta Shield',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'ntc_slimshape' => [
      'nome' => 'Slim Shape',
      'img'  => 'https://thumbor.cartpanda.com/ApTVTdPFQqTWvQwzO55ivVfg-dE=/800x0/https://assets.mycartpanda.com/static/products_images/50/1a/cd/1746801372.png'
  ],
  'ntc_sugarreverse' => [
      'nome' => 'Sugar Reverse',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'sip_burnjaro' => [
      'nome' => 'Burn Jaro',
      'img'  => 'https://thumbor.cartpanda.com/42PZTM4K9TtZn-wwXyLdmU2eHe8=/800x0/https://assets.mycartpanda.com/static/products_images/61/3a/43/1741286694.png'
  ],
  'sip_meltjaro' => [
      'nome' => 'Melt Jaro',
      'img'  => 'https://thumbor.cartpanda.com/ZYFxy8O2GhbLJNR4xzBo4MhM6Bg=/800x0/https://assets.mycartpanda.com/static/products_images/b4/f2/41/1750770280.png'
  ],
  'sip_glucolife360' => [
      'nome' => 'Gluco Life 360',
      'img'  => 'https://thumbor.cartpanda.com/2Rd_t8zS1Sc4fp2xQQG6lsfqUGg=/800x0/https://assets.mycartpanda.com/static/products_images/08/86/87/1748635866.png'
  ],
  'sip_ironboost' => [
      'nome' => 'Iron Boost',
      'img'  => 'https://thumbor.cartpanda.com/mtr-Mytkdr1Uc8qkWgHA1O9WS6k=/800x0/https://assets.mycartpanda.com/static/products_images/16/dd/2f/1757366072.png'
  ],
  'vg_systemamerican' => [
      'nome' => 'System American',
      'img'  => 'https://thumbor.cartpanda.com/3fbJrgRIpCijRXYyKuKaBkihgqg=/800x0/https://assets.mycartpanda.com/static/products_images/b8/bb/77/1749059879.png'
  ],
  'vg_trustearn' => [
      'nome' => 'Trust Earn',
      'img'  => 'https://thumbor.cartpanda.com/DsoLG82NyhwN5IBQu2GDTpsg0QI=/800x0/https://assets.mycartpanda.com/static/products_images/2c/49/a0/1758738860.png'
  ],
  'vg_greentracker' => [
      'nome' => 'Green Tracker',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'vg_energycore' => [
      'nome' => 'Energy Core',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'v2g_glcapsmv1' => [
      'nome' => 'GL Caps MV1',
      'img'  => 'https://thumbor.cartpanda.com/A_9xlDKj08M7yGTJkewVZURRmzc=/800x0/https://assets.mycartpanda.com/static/products_images/a4/b2/75/1751501292.png'
  ],
  'v2g_glcapsmv2' => [
      'nome' => 'GL Caps MV2',
      'img'  => 'https://thumbor.cartpanda.com/A_9xlDKj08M7yGTJkewVZURRmzc=/800x0/https://assets.mycartpanda.com/static/products_images/a4/b2/75/1751501292.png'
  ],
  'bhv_lipomax' => [
      'nome' => 'Lipo Max',
      'img'  => 'https://thumbor.cartpanda.com/6Q1Z-I-Eu254MlDju0o1YEGFS9g=/800x0/https://assets.mycartpanda.com/static/products_images/1c/57/a4/1753288181.png'
  ],
  'bhv_iqblastpro' => [
      'nome' => 'IQ Blast Pro',
      'img'  => 'https://thumbor.cartpanda.com/ic9WFObzSJsn39CHvNVKXgH4xR8=/800x0/https://assets.mycartpanda.com/static/products_images/69/c4/18/1753293523.png'
  ],
  'bhv_sugarwise' => [
      'nome' => 'Sugar Wise',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_primepulsemale' => [
      'nome' => 'Prime Pulse Male',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_fungizero' => [
      'nome' => 'Fungi Zero',
      'img'  => 'https://thumbor.cartpanda.com/BjmvAiBMLbNma2EFbQIJCC9uaDY=/800x0/https://assets.mycartpanda.com/static/products_images/0a/62/4a/1760726334.png'
  ],
  'bhv_prostaprime' => [
      'nome' => 'Prosta Prime',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_lipoextreme' => [
      'nome' => 'Lipo Extreme',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_lipogummy' => [
      'nome' => 'Lipo Gummy',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_visiummax' => [
      'nome' => 'Visium Max',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_prostaprimess' => [
      'nome' => 'Prosta Prime SS',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_arthrocel' => [
      'nome' => 'Arthrocell',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_nerverestore' => [
      'nome' => 'Nerve Restore',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_memoblast' => [
      'nome' => 'Memo Blast',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_glp1max' => [
      'nome' => 'GLP1 Max',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'bhv_lipocorpus' => [
      'nome' => 'Lipo Corpus',
      'img'  => 'https://maiver.us/wp-content/uploads/2025/08/cropped-Frame-29.png'
  ],
  'ntc_leanshape' => [
      'nome' => 'Lean Shape',
      'img'  => 'https://assets.mycartpanda.com/static/products_images/b5/3a/4a/1753384717.png'
  ],
  'hpg_sveltavenastra' => [
      'nome' => 'Svelta Venastra',
      'img'  => 'https://thumbor.cartpanda.com/BuTtZxiCoGuJiew2WN5q-Q3Symo=/800x0/https://assets.mycartpanda.com/static/products_images/86/1b/9c/1754004703.png'
  ],
  'hpg_levinasilka' => [
      'nome' => 'Levina Silka',
      'img'  => 'https://assets.mycartpanda.com/static/products_images/75/df/b3/1759697593.png'
  ],
  'hpg_revitra' => [
      'nome' => 'Revitra',
      'img'  => 'https://thumbor.cartpanda.com/zZ27LT5509ihYUpyDuBeQzrSS10=/800x0/https://assets.mycartpanda.com/static/products_images/f8/9b/5e/1756947432.jpg'
  ],
  'hpg_cardiobalance' => [
      'nome' => 'Cardio Balance',
      'img'  => 'https://assets.mycartpanda.com/static/products_images/cf/2a/d9/1760153350.png'
  ],
  'gvm_jointsana' => [
      'nome' => 'Joint Sana',
      'img'  => 'https://thumbor.cartpanda.com/RDdrZonsosQ9k0LEUopVgnoloRY=/800x0/https://assets.mycartpanda.com/static/products_images/68/08/66/1753286353.png'
  ],
  'blc_strongstream' => [
      'nome' => 'Strong Stream',
      'img'  => 'https://thumbor.cartpanda.com/T_oXm7u-BwDo7zBvE6gGpoQhjoo=/800x0/https://assets.mycartpanda.com/static/products_images/1f/21/2b/1742573251.png'
  ],
  'blc_strongflow' => [
      'nome' => 'Strong Flow',
      'img'  => 'https://thumbor.cartpanda.com/dDwyNVPlh90zosV0yG6flPKBpro=/800x0/https://assets.mycartpanda.com/static/products_images/02/d4/b4/1756729620.png'
  ],
  'eep_axionis' => [
      'nome' => 'Axionis',
      'img'  => 'https://thumbor.cartpanda.com/zbY-VaE5td25PCFl1bZZouScawI=/800x0/https://assets.mycartpanda.com/static/products_images/5b/5e/0f/1744138745.png'
  ],
  'red_burnflow' => [
      'nome' => 'Burn Flow',
      'img'  => 'https://thumbor.cartpanda.com/2sqSUiIdKojtTz9rxdktWh5q_Ek=/800x0/https://assets.mycartpanda.com/static/products_images/65/3f/ec/1755646923.png'
  ],
  'red_slimfuse' => [
      'nome' => 'Slim Fuse',
      'img'  => 'https://thumbor.cartpanda.com/heScxsXWWERuswhFWPK1QYdJZT4=/800x0/https://assets.mycartpanda.com/static/products_images/63/88/08/1761177730.png'
  ],
  'fmg_folifix' => [
      'nome' => 'FoliFix',
      'img'  => 'https://thumbor.cartpanda.com/bWI89KBTHo1YNBr0e5OR7NMtQNg=/800x0/https://assets.mycartpanda.com/static/products_images/53/3e/3f/1746559252.png'
  ],
  'dgm_sugarvita' => [
      'nome' => 'Sugar Vita',
      'img'  => 'https://thumbor.cartpanda.com/wQJNYSd0YC_B7TTm1W2vGiUfQjA=/800x0/https://assets.mycartpanda.com/static/products_images/d3/9b/20/1755645653.png'
  ],
  'zen_jointflex' => [
      'nome' => 'JointFlex',
      'img'  => 'https://thumbor.cartpanda.com/bc6rv1Y_LDPyAcBQK36QaYlT718=/800x0/https://assets.mycartpanda.com/static/products_images/3b/41/92/1757036418.png'
  ],
  'nwm_ironpulse' => [
      'nome' => 'IronPulse',
      'img'  => 'https://thumbor.cartpanda.com/N81UQ8ke6TLPrnbXygXnM2Tr7VM=/800x0/https://assets.mycartpanda.com/static/products_images/75/5c/1b/1757451004.png'
  ],
  'nwm_vigorboost' => [
      'nome' => 'VigorBoost',
      'img'  => 'https://assets.mycartpanda.com/static/products_images/b2/bd/63/1758141388.png'
  ],
  'nwm_metalean' => [
      'nome' => 'MetaLean',
      'img'  => 'https://thumbor.cartpanda.com/2iVXNqdoVgsZ4SDoSSfm1PikgQY=/800x0/https://assets.mycartpanda.com/static/products_images/1e/9d/c7/1759948736.png'
  ],
  'inf_glucodelete' => [
      'nome' => 'Gluco Delete',
      'img'  => 'https://assets.mycartpanda.com/static/products_images/a2/b1/35/1757630244.png'
  ],
  'inf_neuromind' => [
      'nome' => 'NeuroMind',
      'img'  => 'https://thumbor.cartpanda.com/MjbYMRlyVuhYRv4JuJeSsI8ZVJ0=/120x0/https://assets.mycartpanda.com/static/products_images/a5/1b/4f/1758310901.png'
  ],
  'sag_spymate' => [
      'nome' => 'Spy Mate',
      'img'  => 'https://production-mundpay.s3.us-east-2.amazonaws.com/products/2025/8/20/fvXK7GyVjn6my1CZyXg2ebYRHy3C1QwToi7Ipjma.png'
  ],
  'dtc_ozemburnmax' => [
      'nome' => 'OzemBurn Max',
      'img'  => 'https://thumbor.cartpanda.com/T3XvA5dWGKK11U1SzMDApu45p-4=/800x0/https://assets.mycartpanda.com/static/products_images/47/11/9c/1758221921.png'
  ],
  'dtc_nowburn' => [
      'nome' => 'NowBurn',
      'img'  => 'https://thumbor.cartpanda.com/4UAel4-f_pdBgDPmOLCJzb3Ha_g=/800x0/https://assets.mycartpanda.com/static/products_images/e8/f4/8b/1759775615.png'
  ],
  'upx_glucoguard' => [
      'nome' => 'GlucoGuard',
      'img'  => 'https://thumbor.cartpanda.com/Izg_t0nxjf6PC_6bUMB9jqMKbXo=/800x0/https://assets.mycartpanda.com/static/products_images/a3/f9/1c/1758172726.png'
  ],
  'amx_protocoloceroazucar' => [
      'nome' => 'Protocolo Cero Azúcar',
      'img'  => 'https://static-media.hotmart.com/Dyztz8OkGm2OgoH_yYF-BNeS6E8=/300x300/smart/filters:format(webp):background_color(white)/hotmart/product_pictures/7fab01e8-ed54-4b32-bb12-ca53a75475a6/ProtocoloCeroAzucar.png?w=920'
  ],
  'mvx_slimvita' => [
      'nome' => 'Slim Vita',
      'img'  => 'https://thumbor.cartpanda.com/xYUCsijSw-gTf9R0zUIkAO2HtZc=/800x0/https://assets.mycartpanda.com/static/products_images/45/38/92/1755709555.jpg'
  ],
  '4mg_apexboost' => [
      'nome' => 'Apex Boost',
      'img'  => 'https://www.checkout-ds24.com/pb/img/merchant_4835859/image/product/ZSKDVE1N.gif'
  ],
  '4mg_apexburn' => [
      'nome' => 'Apex Burn',
      'img'  => 'https://www.checkout-ds24.com/pb/img/merchant_4835859/image/product/TYZAY9LK.png'
  ],
  'nex_tiklynvox' => [
      'nome' => 'Tik Lynvox 2.0',
      'img'  => 'https://static-media.hotmart.com/9Ar79Dgc7aAR36onrXrjJpCv_jg=/300x300/filters:quality(100)/hotmart/product_pictures/f5b43cb3-03ec-4b0d-be51-6281ae875614/ChatGPTImage16deoutde202512_22_16.png'
  ],
  'dip_lasabiduriadesalomon' => [
      'nome' => 'La Sabiduria de Salomon',
      'img'  => 'https://static-media.hotmart.com/1bu6LeLzb0EBjiNYRWJF3wca7wE=/300x300/filters:quality(100)/hotmart/product_pictures/15c89670-3ff7-4e10-8826-903a527eb1cc/LOGOWOSLATAM1600x1600px.png'
  ]
];

// Mescla produtos: primeiro fallback, depois banco (banco sobrescreve fallback)
$produtos = array_merge($produtos_fallback, $produtos);

// Busca dados de cada produto (tabelas _rec e _ap)
$dados = [];
$data_inicio_escaped = escape($conn, $data_inicio);
$data_fim_escaped = escape($conn, $data_fim);

foreach ($tabelas as $tabela) {
  $tabela_escaped = escape($conn, $tabela);
  $tabela_rec = $tabela . '_rec';
  $tabela_ap = $tabela . '_ap';
  
  $leads_rec = 0;
  $leads_ap = 0;
  
  // Verifica se a tabela _rec existe e busca dados
  $check_rec = $conn->query("SHOW TABLES LIKE '{$tabela_rec}'");
  if ($check_rec && $check_rec->num_rows > 0) {
    $query_rec = "SELECT COUNT(*) AS cnt FROM `{$tabela_rec}`
      WHERE STR_TO_DATE(data_compra, '%d/%m/%Y')
      BETWEEN STR_TO_DATE('{$data_inicio_escaped}', '%d/%m/%Y')
      AND STR_TO_DATE('{$data_fim_escaped}', '%d/%m/%Y')";
    
    $result_rec = $conn->query($query_rec);
    if ($result_rec) {
      $row_rec = $result_rec->fetch_assoc();
      $leads_rec = (int)$row_rec['cnt'];
    }
  }
  
  // Verifica se a tabela _ap existe e busca dados
  $check_ap = $conn->query("SHOW TABLES LIKE '{$tabela_ap}'");
  if ($check_ap && $check_ap->num_rows > 0) {
    $query_ap = "SELECT COUNT(*) AS cnt FROM `{$tabela_ap}`
      WHERE STR_TO_DATE(data_compra, '%d/%m/%Y')
      BETWEEN STR_TO_DATE('{$data_inicio_escaped}', '%d/%m/%Y')
      AND STR_TO_DATE('{$data_fim_escaped}', '%d/%m/%Y')";
    
    $result_ap = $conn->query($query_ap);
    if ($result_ap) {
      $row_ap = $result_ap->fetch_assoc();
      $leads_ap = (int)$row_ap['cnt'];
    }
  }
  
  $total = $leads_rec + $leads_ap;
  
  // Adiciona aos dados se o produto estiver cadastrado ou se houver pelo menos uma tabela existente
  $produto_cadastrado = in_array($tabela, $produtos_cadastrados);
  $tem_tabela = (isset($check_rec) && $check_rec->num_rows > 0) || (isset($check_ap) && $check_ap->num_rows > 0);
  
  // Sempre adiciona se o produto estiver cadastrado, ou se houver tabelas existentes
  if ($produto_cadastrado || $tem_tabela) {
    $dados[] = [
      'Operação' => $tabela,
      'Leads_Rec' => $leads_rec,
      'Leads_Ap' => $leads_ap,
      'Total' => $total
    ];
  }
}

// Exporta como Excel se solicitado
if ($exportar) {
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=relatorio_leads.xls");
  echo "<table border='1'><tr><th>Operação</th><th>Leads Recuperação</th><th>Leads Aprovados</th><th>Total</th></tr>";
  foreach ($dados as $item) {
    $operacao = htmlspecialchars($item['Operação']);
    $leads_rec = (int)($item['Leads_Rec'] ?? 0);
    $leads_ap = (int)($item['Leads_Ap'] ?? 0);
    $total = (int)($item['Total'] ?? 0);
    echo "<tr><td>{$operacao}</td><td>{$leads_rec}</td><td>{$leads_ap}</td><td>{$total}</td></tr>";
  }
  echo "</table>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Leads - MAIVER</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      min-height: 100vh;
      background-color: #f5f5f7;
    }
    .layout-wrapper {
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      width: 230px;
      background: #1f2933;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 20px 15px;
    }
    .sidebar .logo {
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
    }
    .sidebar .nav-link {
      color: #cbd2d9;
      padding: 8px 10px;
      border-radius: 6px;
      margin-bottom: 4px;
      font-size: 0.95rem;
    }
    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background: #3e4c59;
      color: #fff;
    }
    .sidebar .nav-link i {
      margin-right: 6px;
    }
    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .user-info {
      color: white;
      margin-right: 15px;
    }
    .container-produtos {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
    }
    .card-op {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 10px;
      text-align: center;
      background: #fff;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
      min-height: 300px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .product-img {
      height: 150px;
      width: 100%;
      object-fit: contain;
      background: #fff;
      padding: 10px;
      border-bottom: 1px solid #eee;
    }
    .product-img-preview {
      max-width: 100px;
      max-height: 100px;
      object-fit: contain;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 5px;
    }
    .card-op .btn {
      z-index: 10;
    }
  </style>
</head>
<body>
  <div class="layout-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        MAIVER Dashboard
      </div>
      <nav class="nav flex-column mb-auto">
        <a href="dashboard.php" class="nav-link active">
          📊 Dashboard
        </a>
        <a href="produtos.php" class="nav-link">
          📦 Produtos
        </a>
      </nav>
      <div class="mt-auto">
        <hr class="border-secondary">
        <div class="small mb-2">
          👤 <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
        </div>
        <a href="logout.php" class="btn btn-outline-light btn-sm w-100">
          Sair
        </a>
      </div>
    </aside>

    <div class="content-wrapper">
      <!-- Navbar superior -->
      <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
          <a class="navbar-brand" href="dashboard.php">
            <strong>📊 Dashboard MAIVER</strong>
          </a>
        </div>
      </nav>

      <div class="container mt-4">
    <?php 
      // Exibe mensagem de sucesso se houver redirect
      if (isset($_GET['msg']) && $_GET['msg'] === 'success' && isset($_GET['text'])): 
    ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['text']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    
    <form method="POST" class="row g-3 align-items-end mb-4">
      <div class="col-md-3">
        <label class="form-label">Data Início</label>
        <input type="date" name="data_inicio" class="form-control"
          value="<?= date('Y-m-d', strtotime(str_replace('/', '-', $data_inicio))) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Data Fim</label>
        <input type="date" name="data_fim" class="form-control"
          value="<?= date('Y-m-d', strtotime(str_replace('/', '-', $data_fim))) ?>" required>
      </div>
      <div class="col-md-auto">
        <button type="submit" class="btn btn-primary">🔍 Pesquisar</button>
      </div>
      <div class="col-md-auto">
        <button type="submit" name="exportar" value="1" class="btn btn-success">⬇️ Exportar Excel</button>
      </div>
    </form>

    <hr>

        <div class="row">
          <?php if (!empty($dados)): ?>
            <?php foreach ($dados as $item): ?>
              <?php
                $operacao = $item['Operação'];
                $prefixo = strtoupper(substr($operacao, 0, 3));
                $legenda = $legendas[$prefixo] ?? 'Sem legenda';
                // Nome e imagem agora vêm apenas da tabela `produtos`
                $produtoNome = $produtos[$operacao]['nome'] ?? 'Produto desconhecido';
                $produtoImg  = $produtos[$operacao]['img'] ?? 'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27200%27 height=%27200%27%3E%3Crect fill=%27%23ddd%27 width=%27200%27 height=%27200%27/%3E%3Ctext fill=%27%23999%27 x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27%3ESem imagem%3C/text%3E%3C/svg%3E';
                $produtoId = $produtos[$operacao]['id'] ?? null;
                $leads_rec = (int)($item['Leads_Rec'] ?? 0);
                $leads_ap = (int)($item['Leads_Ap'] ?? 0);
                $total = (int)($item['Total'] ?? 0);
              ?>
              <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card card-op border-primary text-center p-3 position-relative">
                  <?php if ($produtoId): ?>
                    <button type="button" class="btn btn-sm btn-warning position-absolute top-0 end-0 m-2" 
                            data-bs-toggle="modal" data-bs-target="#editModal<?= $produtoId ?>"
                            title="Editar Produto">
                      <i class="bi bi-pencil"></i>
                    </button>
                  <?php endif; ?>
                  <img src="<?= $produtoImg ?>" alt="<?= $produtoNome ?>" class="img-fluid product-img">
                  <h5 class="card-title text-primary"><?= $produtoNome ?></h5>
                  <p class="card-subtitle mb-1 text-muted small"><?= $legenda ?></p>
                  <div class="mt-2">
                    <p class="card-text mb-1">
                      <span class="badge bg-info">Recuperação: <?= $leads_rec ?></span>
                    </p>
                    <p class="card-text mb-1">
                      <span class="badge bg-success">Aprovados: <?= $leads_ap ?></span>
                    </p>
                    <p class="card-text fs-4 mt-2">
                      <strong>Total: <?= $total ?></strong>
                    </p>
                  </div>
                </div>
              </div>
              
              <?php if ($produtoId): ?>
                <!-- Modal de Edição no Dashboard -->
                <div class="modal fade" id="editModal<?= $produtoId ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="produtos.php">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id" value="<?= $produtoId ?>">
                        <input type="hidden" name="redirect" value="dashboard.php">
                        <div class="modal-header">
                          <h5 class="modal-title">Editar Produto: <?= htmlspecialchars($produtoNome) ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Tabela</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($operacao) ?>" disabled>
                            <small class="text-muted">A tabela não pode ser alterada</small>
                          </div>
                          <?php
                            // Busca dados atuais do produto
                            $query_atual = "SELECT nome, imagem_url, ativo FROM produtos WHERE id = " . (int)$produtoId;
                            $result_atual = $conn->query($query_atual);
                            $produto_atual = $result_atual ? $result_atual->fetch_assoc() : null;
                          ?>
                          <?php if ($produto_atual): ?>
                            <div class="mb-3">
                              <label class="form-label">Nome do Produto</label>
                              <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($produto_atual['nome']) ?>" required>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">URL da Imagem</label>
                              <input type="url" class="form-control" name="imagem_url" value="<?= htmlspecialchars($produto_atual['imagem_url']) ?>" required>
                              <div class="mt-2">
                                <img src="<?= htmlspecialchars($produto_atual['imagem_url']) ?>" 
                                     alt="Preview" 
                                     class="product-img-preview"
                                     style="max-width: 100px; max-height: 100px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px; padding: 5px;"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect fill=%27%23ddd%27 width=%27100%27 height=%27100%27/%3E%3Ctext fill=%27%23999%27 x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27%3ESem imagem%3C/text%3E%3C/svg%3E'">
                              </div>
                            </div>
                            <div class="mb-3">
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ativo" id="ativo<?= $produtoId ?>" <?= $produto_atual['ativo'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ativo<?= $produtoId ?>">
                                  Produto Ativo
                                </label>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12">
              <div class="alert alert-warning text-center">Nenhum dado encontrado no período selecionado.</div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

