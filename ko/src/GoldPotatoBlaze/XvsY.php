<?php
namespace GoldPotatoBlaze;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\entity\Effect;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\PlayerInventory;
class XvsY extends PluginBase implements Listener{
	public function onEnable(){
		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder(), 0744, true);
		}
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML,
			array(
				"접수 에 사용 하는 블록 의 ID"=>"129",
				"XvsY"=>"2vs2",
				"텔레포트 지점"=>array("플레이어1"=>array("x"=>128,"y"=>4,"z"=>128,"월드"=>"world"),"플레이어2"=>array("x"=>128,"y"=>4,"z"=>128,"월드"=>"world"),"플레이어3"=>array("x"=>128,"y"=>4,"z"=>128,"월드"=>"world"),"플레이어4"=>array("x"=>128,"y"=>4,"z"=>128,"월드"=>"world")),
				"체력"=>"20",
				"최대 체력"=>"20",
				"제한 시간"=>"120",
				"아이템 과 효과 지우기"=>"on",
				"아이템 의 배포"=>array("아이템1(ID:Meta:개수)"=>"0:0:0","아이템2(ID:Meta:개수)"=>"0:0:0"),
				"방어구"=>array("헬멧"=>0,"가슴 판"=>0,"레깅스"=>0,"부츠"=>0),
				"효과"=>array("효과1(ID:초:강도)"=>"0:0:0","효과2(ID:초:강도)"=>"0:0:0"),
				));
		$this->system["GamesRun"] = "off";
		$this->system["MenberTima"] = "";
		$this->system["Teams"] = "";
		$this->system["Teams2"] = "";
		$this->system["Motion"] = "off";
		$vsvs = $this;
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function XvsYTouch(PlayerInteractEvent $event){
		if($event->getBlock()->getId() == $this->config->get("접수 에 사용 하는 블록 의 ID")){
			$vs = explode("vs",$this->config->get("XvsY"));
			$vs1 = "";
			for($i = 0;$i < count($vs);$i++){
				$vs1 = $vs1 + $vs[$i];
			}
			if($this->system["GamesRun"] == "on"){
				$event->getPlayer()->sendMessage("§d[".$this->config->get("XvsY")."] §c만원 입니다");
			}else{
				$member = explode(":",$this->system["MenberTima"]);
				if(strpos($this->system["MenberTima"],":")){
					$member = explode(":",$this->system["MenberTima"]);
					$a = $member[count($member) - 1] == $event->getPlayer()->getName();
				}else{
					$a = strpos($this->system["MenberTima"],$event->getPlayer()->getName());
				}
				if(strpos($this->system["MenberTima"],":".$event->getPlayer()->getName().":")||$a||$member[0] == $event->getPlayer()->getName()){
					$event->getPlayer()->sendMessage("§d[".$this->config->get("XvsY")."] §c이미 접수 가 완료 됩니다");
				}else{
					if($this->system["MenberTima"] != ""){
						$this->system["MenberTima"] = $this->system["MenberTima"].":".$event->getPlayer()->getName();
					}else{
						$this->system["MenberTima"] = $event->getPlayer()->getName();
					}
					unset($player);
					if(count(explode(":",$this->system["MenberTima"])) == $vs1){
						$down = 3;
						$member1 = explode(":",$this->system["MenberTima"]);
						$player = array($this->getServer()->getPlayer($member1[0]));
						for($i1 = 1; $i1 < $vs1 ;$i1++){
							$player1 = $this->getServer()->getPlayer($member1[$i1]);
							array_push($player,$player1);
						}
						$i1 = 0;
						$i2 = 0;
						$i3 = 0;
						unset($a);
						for($i = 0;$i < $vs1;$i++){
							if($vs[$i1] > $i2){
								$a = $this->system["Teams"];
								$a[$member1[$i3]] = $i1;
								$this->system["Teams"][$member1[$i3]] = $i1;
								$this->system["Teams2"][$member1[$i3]] = $i1;
								$i3++;
								$i2++;
							}else{
								$i1++;
								$i--;
								$i2=0;
							}
						}
						for ( $i = 0; $i < 4 ; $i++){
							$task = new XvsYCountDown($this,$player,$down,$this->system,$this->getServer(),$this->config);
							$this->getServer()->getScheduler()->scheduleDelayedTask($task,$i*20);
							$down--;
						}
						$i = 1;
						foreach($player as $playertp){
							$this->getServer()->loadLevel($this->config->getAll()["텔레포트 지점"]["플레이어".$i]["월드"]);
							$level = Server::getInstance()->getLevelByName($this->config->getAll()["텔레포트 지점"]["플레이어".$i]["월드"]);
							$xyz1 = new Position($this->config->getAll()["텔레포트 지점"]["플레이어".$i]["x"],$this->config->getAll()["텔레포트 지점"]["플레이어".$i]["y"],$this->config->getAll()["텔레포트 지점"]["플레이어".$i]["z"],$level);
							$playertp->teleport($xyz1);
							$i++;
						}
						$this->system["Motion"] = "on";
						foreach($member1 as $name){
							if(isset($message)){
								if($name1 ==  $this->system["Teams"][$name]){
									$message = $message."&".$name;
								}else{
									$message = $message."§cvs§a".$name;
								}
							}else{
								$message = $name;
							}
							$name1 = $this->system["Teams"][$name];
						}
						Server::getInstance()->broadcastMessage("§a§d[".$this->config->get("XvsY")."] §e".$message." 경기 가 시작 됩니다");
						foreach($player as $gm0){
							$gm0->setGamemode(0);
						}
						$this->system["GamesRun"] = "on";
						if($this->config->get("아이템 과 효과 지우기") == "on"){
							foreach($player as $ei){
								$ei->removeAllEffects();
								$ei->getInventory()->clearAll();
							}
						}
						foreach($this->config->getAll()["아이템 의 배포"] as $item){
							$item = explode(":",$item);
							$item = Item::get($item[0],$item[1],$item[2]);
							foreach($player as $i){
								$i->getInventory()->addItem($item);
							}
						}
						foreach($this->config->getAll()["효과"] as $effect){
							$effect = explode(":",$effect);
							if($effect[0] != 0){
								foreach($player as $eff){
									$eff->addEffect(Effect::getEffect($effect[0])->setDuration($effect[1] * 20)->setAmplifier($effect[2])->setVisible(true));
									$eff->addEffect(Effect::getEffect(11)->setDuration(3 * 20)->setAmplifier(10)->setVisible(false));
								}
							}
						}
						foreach($player as $player1){
							$player1->getInventory()->setArmorItem(0,Item::get($this->config->getAll()["방어구"]["헬멧"],0,1));
							$player1->getInventory()->setArmorItem(1,Item::get($this->config->getAll()["방어구"]["가슴 판"],0,1));
							$player1->getInventory()->setArmorItem(2,Item::get($this->config->getAll()["방어구"]["레깅스"],0,1));
							$player1->getInventory()->setArmorItem(3,Item::get($this->config->getAll()["방어구"]["부츠"],0,1));
							$player1->getInventory()->sendArmorContents($player1);
						}
						foreach($player as $player1){
							$player1->setMaxHealth($this->config->get("최대 체력"));
							$player1->save();
						}
						foreach($player as $player1){
							$player1->setHealth($this->config->get("체력"));
							$player1->save();
						}
					}else{
						Server::getInstance()->broadcastMessage("§e§d[".$this->config->get("XvsY")."]§e ".$event->getPlayer()->getName()." 가 접수를 했습니다");
					}
				}
			}
		}
	}
	public function XvsYDamage(EntityDamageEvent $event){
		if($event instanceof EntityDamageByEntityEvent){
			if($this->system["GamesRun"] == "on"){
				$a = $event->getEntity()->getName();
				$b = $event->getDamager()->getPlayer()->getName();
				if(strpos($this->system["MenberTima"],":")){
					$member = explode(":",$this->system["MenberTima"]);
					$c = $member[count($member) - 1] == $event->getEntity()->getName();
				}else{
					$c = strpos($this->system["MenberTima"],$event->getEntity()->getName());
				}
				if(strpos($this->system["MenberTima"],":".$event->getEntity()->getName().":")||$c||$member[0] == $event->getEntity()->getName()){
					if(isset($this->system["Teams"][$a])){
						if(isset($this->system["Teams"][$b])){
							if($this->system["Teams"][$a] == $this->system["Teams"][$b]){
								$event->getDamager()->getPlayer()->sendPopup("§c같은 팀 입니다");
								$event->setCancelled(true);
							}
						}
					}else{
						$event->getDamager()->getPlayer()->sendMessage("§c경기 중입니다");
						$event->setCancelled(true);
					}
				}
			}
		}
	}
	public function XvsYPlayerMove(PlayerMoveEvent $event){
		$player = $event->getPlayer();
		$member = explode(":",$this->system["MenberTima"]);
		if($this->system["Motion"] == "on"){
			if(strpos($this->system["MenberTima"],":".$player->getName().":")||$member[count($member) - 1] == $player->getName()||$member[0] == $event->getPlayer()->getName()){
				if(count($player->usedChunks) >= 56){
					$event->setCancelled(true);
				}
			}
		}
	}
	public function XvsYQuit(PlayerQuitEvent $event) {
		$member = explode(":",$this->system["MenberTima"]);
		if(strpos($this->system["MenberTima"],":")){
			$member = explode(":",$this->system["MenberTima"]);
			$a = $member[count($member) - 1] == $event->getPlayer()->getName();
		}else{
			$a = strpos($this->system["MenberTima"],$event->getPlayer()->getName());
		}
		if(strpos($this->system["MenberTima"],":".$event->getPlayer()->getName().":")||$a||$member[0] == $event->getPlayer()->getName()){
			$member = explode(":",$this->system["MenberTima"]);
			if($this->system["GamesRun"] == "on"){
				Server::getInstance()->broadcastMessage("§d[".$this->config->get("XvsY")."]§e ".$event->getPlayer()->getName()." 가 퇴출 했습니다");
				$c = $this->system["Teams"];
				unset($c[$event->getPlayer()->getName()]);
				$this->system["Teams"] = $c;
				$a = $this->system["Teams"];
				$a = array_unique($a);
				$a = array_values($a);
				if(count($a) <= 1){
					$i = 0;
					foreach($this->system["Teams2"] as $name => $wins){
						if($i == 0){
							if($wins == $a[0]){
								$wins1 = $name;
								$i++;
							}
						}else{
							if($wins == $a[0]){
								$wins1 = $wins1."&".$name;
							}
						$i++;
						}
					}
					Server::getInstance()->broadcastMessage("§e§d[".$this->config->get("XvsY")."]§e경기 가 종료되었습니다\n§d[".$this->config->get("XvsY")."]§e".$wins1." 이 승리 했습니다");
					unset($this->system["MenberTima"]);
					$this->system["MenberTima"] = "";
					unset($this->system["GamesRun"]);
					$this->system["GamesRun"] = "";
					unset($this->system["Teams"]);
					$this->system["Teams"] = "";
					unset($this->system["Teams2"]);
					$this->system["Teams2"] = "";
					$this->getServer()->getScheduler()->cancelTasks($this);
					foreach($member as $tp){
						$tp = $this->getServer()->getPlayer($tp);
						if($tp !== null){
							$tp->teleport($this->getServer()->getDefaultLevel()->getSpawn());
							if($this->config->get("아이템 과 효과 지우기") == "on"){
								$tp->removeAllEffects();
								$tp->getInventory()->clearAll();
							}
						}
					}
				}
			}else{
				if($this->system["MenberTima"] == $event->getPlayer()->getName()){
					unset($this->system["MenberTima"]);
					$this->system["MenberTima"] = "";
				}else{
					if(strpos($this->system["MenberTima"],":")){
						if(strpos($this->system["MenberTima"],":".$event->getPlayer()->getName().":")){
							$newmember = str_replace(":".$event->getPlayer()->getName(),"",$this->system["MenberTima"]);
							$this->system["MenberTima"] = $newmember;
						}else{
							if($member[substr_count($this->system["MenberTima"],":") - 1]){
								$newmember = str_replace(":".$event->getPlayer()->getName(),"",$this->system["MenberTima"]);
								$this->system["MenberTima"] = $newmember;
							}else{
								if($member[0] == $event->getPlayer()->getName()){
									$newmember = str_replace(":".$event->getPlayer()->getName(),"",$this->system["MenberTima"]);
									$this->system["MenberTima"] = $newmember;
								}
							}
						}
					}
				}
			}
		}
	}
	public function XvsYDeath(PlayerDeathEvent $event){
		$member = explode(":",$this->system["MenberTima"]);
		if(strpos($this->system["MenberTima"],":")){
			$member = explode(":",$this->system["MenberTima"]);
			$a = $member[count($member) - 1] == $event->getEntity()->getName();
		}else{
			$a = strpos($this->system["MenberTima"],$event->getEntity()->getName());
		}
		if(strpos($this->system["MenberTima"],":".$event->getEntity()->getName().":")||$a||$member[0] == $event->getEntity()->getName()){
			if($this->system["GamesRun"] == "on"){
				$member = explode(":",$this->system["MenberTima"]);
				Server::getInstance()->broadcastMessage("§d[".$this->config->get("XvsY")."]§e ".$event->getEntity()->getName()." 사망 했습니다");
				$c = $this->system["Teams"];
				unset($c[$event->getEntity()->getName()]);
				$this->system["Teams"] = $c;
				$a = $this->system["Teams"];
				$a = array_unique($a);
				$a = array_values($a);
				if(count($a) <= 1){
					$i = 0;
					foreach($this->system["Teams2"] as $name => $wins){
						if($i == 0){
							if($wins == $a[0]){
								$wins1 = $name;
								$i++;
							}
						}else{
							if($wins == $a[0]){
								$wins1 = $wins1."&".$name;
							}
						$i++;
						}
					}
					Server::getInstance()->broadcastMessage("§e§d[".$this->config->get("XvsY")."]§e경기 가 종료되었습니다\n§d[".$this->config->get("XvsY")."]§e".$wins1." 가 승리 했습니다");
					unset($this->system["MenberTima"]);
					$this->system["MenberTima"] = "";
					unset($this->system["GamesRun"]);
					$this->system["GamesRun"] = "";
					unset($this->system["Teams"]);
					$this->system["Teams"] = "";
					unset($this->system["Teams2"]);
					$this->system["Teams2"] = "";
					$this->getServer()->getScheduler()->cancelTasks($this);
					foreach($member as $tp){
						$tp = $this->getServer()->getPlayer($tp);
						if($tp !== null){
							$tp->teleport($this->getServer()->getDefaultLevel()->getSpawn());
							if($this->config->get("아이템 과 효과 지우기") == "on"){
								$tp->removeAllEffects();
								$tp->getInventory()->clearAll();
							}
						}
					}
				}
			}
		}
	}
}
class XvsYCountDown extends PluginTask{
	public function __construct(PluginBase $owner,$player,$down,$config,$getServer,Config $setting){
		parent::__construct($owner);
		$this->this = $owner;
		$this->player = $player;
		$this->down = $down;
		$this->owner = $owner;
		$this->getServer = $getServer;
		$this->setting = $setting;
	}
	public function onRun($tick){
		foreach($this->player as $player){
			$player->sendPopup("§l".$this->down);
		}
		if($this->down == 0){
			$this->this->system["Motion"] = "off";
			$down = $this->setting->get("제한 시간");
			for ( $i = 0; $i < $this->setting->get("제한 시간") + 1 ; $i++){
				$task = new XvsY2CountDown($this->this,$this->player,$down,$this->owner,$this->getServer,$this->setting);
				$this->getServer->getScheduler()->scheduleDelayedTask($task,$i*20);
				foreach($this->player as $player){
					$player->sendTip("§c§lstart!!");
					$player->sendTip("§c§lstart!!");
				}
				$down--;
			}
		}
	}
}
class XvsY2CountDown extends PluginTask{
	public function __construct(PluginBase $owner,$player,$down,$config,$getServer,$vs){
		parent::__construct($owner);
		$this->this = $owner;
		$this->player = $player;
		$this->down = $down;
		$this->getServer = $getServer;
		$this->vs = $vs;
	}
	public function onRun($tick){
		foreach($this->player as $player){
			$player->sendPopup("§l".$this->down);
		}
		if($this->down == 0){
			Server::getInstance()->broadcastMessage("§e[".$this->vs->get("XvsY")."]마감 시간");
			unset($this->this->system["MenberTima"]);
			$this->this->system["MenberTima"] = "";
			unset($this->this->system["GamesRun"]);
			$this->this->system["GamesRun"] = "";
			unset($this->this->system["Teams"]);
			$this->this->system["Teams"] = "";
			unset($this->this->system["Teams2"]);
			$this->this->system["Teams2"] = "";
			foreach($this->player as $tp){
				if($tp !== null){
					$tp->teleport($this->getServer->getDefaultLevel()->getSpawn());
				}
			}
		}
	}
}
