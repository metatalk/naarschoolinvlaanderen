<?php
ini_set('display_errors', 'On');

require dirname(__DIR__, 1).'/vendor/autoload.php';

if(!defined('LAZER_DATA_PATH')) {
  define('LAZER_DATA_PATH', dirname(__DIR__, 1).'/databases/');
}

use Lazer\Classes\Database as Lazer;
use Lazer\Classes\Relation;

$typeonderwijs = array('lager','secundair');
$function = !empty($_GET['function']) ? $_GET['function'] : 'loadopleidingen';
$year = !empty($_GET['year']) ? $_GET['year'] : date("Y");

if(empty($_GET['type']) || !in_array($_GET['type'],$typeonderwijs)) {
  echo 'Geen geldig type onderwijs...';
  exit;
}

if(!empty($_GET['gemeenten'])) {
  $filtergemeenten = explode(',', $_GET['gemeenten']);
} else {
  $filtergemeenten = false;
}

if(!empty($_GET['opleidingen'])) {
  $filteropleidingen = explode(',', $_GET['opleidingen']);
} else {
  $filteropleidingen = false;
}

//echo '<pre>';
switch($function) {
  case 'loadopleidingen':
    $results = loadopleidingen($_GET['type'],$year,$filtergemeenten,$filteropleidingen);
  break;
  case 'loadgemeentes':
    $results = loadgemeentes($_GET['type'],$year,$filtergemeenten);
  break;
  case 'loadgemeentenlijst':
    $results = loadgemeentenlijst($_GET['type'],$filtergemeenten);
  break;
  default:
    echo 'Geen functie';
  break;
}

echo json_encode($results);

function definetable() {
  $type = $_GET['type'];
  $table = 'opleidingen'.$type;
  return $table;
}

function loadopleidingen($type,$year,$filtergemeente=false,$filteropleidingen=false) {
  Relation::table(definetable())->belongsTo('vestigingen')->localKey('vestiging_id')->foreignKey('id')->setRelation();
  Relation::table(definetable())->hasMany('entries'.$year)->localKey('id')->foreignKey('opleiding_id')->setRelation();
  $opleidingen = Lazer::table(definetable())->with('vestigingen');

  if(!empty($filteropleidingen) && is_array($filteropleidingen)) {
    $opleidingen = $opleidingen->where('opleiding','IN',$filteropleidingen);
  }

  if(!empty($filtergemeente) && is_array($filtergemeente)) {
    $opleidingen = $opleidingen->where('gemeente','IN',$filtergemeente);
  }

  $opleidingen = $opleidingen->with('entries'.$year)->findAll()->asArray();

  foreach($opleidingen as $key => $opleiding) {
    $opleidingen[$key]['entries'] = $opleiding['Entries'.$year]->findAll()->asArray();
    unset($opleidingen[$key]['Entries'.$year]);
  }

  return $opleidingen;
}

function loadgemeentes($type=false,$year,$filtergemeenten) {

  $opleidingen = array();

  Relation::table('vestigingen')->hasMany('opleidingen'.$type)->localKey('id')->foreignKey('vestiging_id')->setRelation();
  Relation::table('opleidingen'.$type)->hasMany('entries'.$year.$type)->localKey('id')->foreignKey('opleiding_id')->setRelation();

  $gemeenten = Lazer::table('vestigingen')->with('opleidingen'.$type)->where('type','=',$type);

  if(!empty($filtergemeenten) && is_array($filtergemeenten)) {
    $gemeenten = $gemeenten->where('gemeente','IN',$filtergemeenten)->orWhere('hoofdgemeente','IN',$filtergemeenten);
  }

  $gemeenten = $gemeenten->findAll()->asArray();

  foreach($gemeenten as $key => $gemeente) {
    $getopleidingen = $gemeente['Opleidingen'.$type]->with('entries'.$year.$type)->orderBy('leerjaar')->findAll();
    $gemeenten[$key]['opleidingen'] = $getopleidingen->asArray();

    foreach($gemeenten[$key]['opleidingen'] as $opleidingkey => $opleiding) {
      if($type == 'secundair' || ($type == 'lager' && insideRange($type,$opleiding,$year))) {
        if($type == 'secundair') {
          $secundairRange = array('006246','006247','6246','6247');
          $gemeenten[$key]['opleidingen'][$opleidingkey]['leerjaar'] = calculateSOLeerjaar($opleiding);
          $gemeenten[$key]['opleidingen'][$opleidingkey]['issecundairhoger'] = in_array($opleiding['administratieve_code'],$secundairRange) ? false : true;
        }
        $gemeenten[$key]['opleidingen'][$opleidingkey]['cijfers'] = calculateCijfers($opleiding['Entries'.$year.$type]->findAll()->asArray(),$type);
        $gemeenten[$key]['opleidingen'][$opleidingkey]['hide'] = hideForParent($gemeenten[$key]['opleidingen'][$opleidingkey]['cijfers']);
        unset($gemeenten[$key]['opleidingen'][$opleidingkey]['Entries'.$year.$type]);
      } else {
        unset($gemeenten[$key]['opleidingen'][$opleidingkey]);
      }
    }

    $gemeenten[$key]['opleidingen'] = array_values($gemeenten[$key]['opleidingen']);

    usort($gemeenten[$key]['opleidingen'], function($a, $b) {
        return strcasecmp($a['leerjaar'], $b['leerjaar']);
    });

    unset($gemeenten[$key]['Opleidingen'.$type]);
    $opleidingen = array_merge($opleidingen,$gemeenten[$key]['opleidingen']);
  }
  shuffle($gemeenten);
  return $gemeenten;

}

function loadgemeentenlijstbasic() {
  $hoofdgemeenten = Lazer::table('vestigingen')->groupBy('hoofdgemeente');
  $lijst = array();
  foreach($hoofdgemeenten as $hoofdgemeentenaam => $gemeenten) {
    if(!in_array($hoofdgemeentenaam,$lijst)) {
      $lijst[] = $hoofdgemeentenaam;
    }
  }
  return $lijst;
}

function loadgemeentenlijst($type,$filtergemeenten) {
  $hoofdgemeenten = Lazer::table('vestigingen')->where('type','=',$type)->with('opleidingen'.$type)->groupBy('hoofdgemeente')->orderBy('hoofdgemeente')->findAll()->asArray();
  $options = array();

  foreach($hoofdgemeenten as $hoofdgemeentenaam => $gemeenten) {
    $deelgemeenten = getUnique($gemeenten,$filtergemeenten,$type);
    if(count($deelgemeenten) > 0) {
      array_push($options,array('label'=>$hoofdgemeentenaam,'options'=>$deelgemeenten));
    }
  }
  return $options;
}

function calculateSOLeerjaar($opleiding) {
  if(empty($opleiding['leerjaar'])) {
    return $opleiding['graad'];
  } elseif(empty($opleiding['graad'])) {
    return $opleiding['leerjaar'];
  } else {

    switch($opleiding['graad']) {
      case 'Eerste graad':
        return $opleiding['leerjaar'] == '1ste leerjaar' ? '1ste middelbaar' : '2de middelbaar';
      break;
      case 'Tweede graad':
        return $opleiding['leerjaar'] == '1ste leerjaar' ? '3de middelbaar' : '4de middelbaar';
      break;
      case 'Derde graad':
        return $opleiding['leerjaar'] == '1ste leerjaar' ? '5de middelbaar' : '6de middelbaar';
      break;
    }
  }
}

function huidigSchooljaar() {
  $currentMonth = date('n');
  $currentYear = date('Y');

  $schooljaar = $currentYear;

  if($currentMonth < 9 && $currentMonth >= 1) {
    $schooljaar = $currentYear - 1;
  }

  return $schooljaar;
}

function insideRange($type,$opleiding,$year) {
  if($type == 'lager' && $opleiding['opleiding'] == 'Kleuteronderwijs') {
    $geboortejaar = (int)substr($opleiding['leerjaar'],-4);
    $timebetween = $year - $geboortejaar;
    if(($timebetween <= 5 && $timebetween >= 2) || empty($opleiding['leerjaar'])) {
      return true;
    } else {
      return false;
    }
  } else {
    return true;
  }
}

function getUnique($gemeenten,$filtergemeenten,$type) {
  $options = array();
  foreach($gemeenten as $gemeente) {
    if(!empty($gemeente->gemeente) && $gemeente->{'Opleidingen'.$type}->findAll()->count() > 0 && !in_array($gemeente->gemeente, array_column($options, 'name'))) {
      $checked = !empty($filtergemeenten) && in_array($gemeente->gemeente,$filtergemeenten) ? true : false;
      array_push($options,array('name'=>$gemeente->gemeente,'value'=>$gemeente->gemeente,'checked' => $checked));
    }
  }
  return $options;
}

function calculateCijfers($cijfers,$type) {

  foreach($cijfers as $key => $cijfer) {
    if(empty($cijfer['plaatsen'])) {
      $cijfers[$key]['plaatsen'] = false;
    }
    $cijfers[$key]['indtotaalplaatsen'] = round($cijfer['plaatsen']/100*$cijfer['percentageind']);
    $cijfers[$key]['indvrijeplaatsen'] = $cijfers[$key]['indtotaalplaatsen'] - $cijfer['plaatsenbezetind'];
    $cijfers[$key]['vrijeplaatsentotaal'] = $cijfer['plaatsen'] - $cijfer['plaatsenbezetind'] - $cijfer['plaatsenbezet'];
    $cijfers[$key]['indmessage'] = $type == 'secundair' ? 'waarvan '. $cijfers[$key]['indvrijeplaatsen'] .' plaatsen voor indicator leerlingen' : 'waarvan '. $cijfers[$key]['indvrijeplaatsen'] .' plaatsen voor indicator leerlingen';
    //$cijfers[$key]['indmessage'] .= htmlspecialchars('');
  }
  return $cijfers;
}

function hideForParent($cijfers) {
  $hide = false;
  foreach($cijfers as $key => $cijfer) {
    if(!empty($cijfer['hide'])) {
      $hide = true;
    }
  }
  return $hide;
}
