<?php

namespace HayaoPVE\task;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandExecutor;
use pocketmine\scheduler\Task;
//use pocketmine\scheduler\CallbackTask;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Fire;
use pocketmine\block\PressurePlate;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Attribute;
use pocketmine\entity\Effect;
use pocketmine\entity\Zombie;
use pocketmine\entity\Skeleton;
use pocketmine\entity\Enderman;
use pocketmine\entity\Villager;
use pocketmine\entity\PigZombie;
use pocketmine\entity\Creeper;
use pocketmine\entity\Spider;
use pocketmine\entity\Witch;
use pocketmine\entity\IronGolem;
use pocketmine\entity\Blaze;
use pocketmine\entity\Slime;
use pocketmine\entity\WitherSkeleton;
use pocketmine\entity\Horse;
use pocketmine\entity\Donkey;
use pocketmine\entity\Mule;
use pocketmine\entity\SkeletonHorse;
use pocketmine\entity\ZombieHorse;
use pocketmine\entity\Stray;
use pocketmine\entity\Husk;
use pocketmine\entity\Mooshroom;
use pocketmine\entity\FallingSand;
use pocketmine\entity\Item as DroppedItem;
use pocketmine\entity\Skin;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\ItemFrameDropItemEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerTextPreSendEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerAnimationEvent;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerHungerChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerUseFishingRodEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\TextContainer;
use pocketmine\event\Timings;
use pocketmine\event\TranslationContainer;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\AnvilInventory;
use pocketmine\inventory\BaseTransaction;
use pocketmine\inventory\BigShapedRecipe;
use pocketmine\inventory\BigShapelessRecipe;
use pocketmine\inventory\CraftingManager;
use pocketmine\inventory\DropItemTransaction;
use pocketmine\inventory\EnchantInventory;
use pocketmine\inventory\FurnaceInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Armor;
use pocketmine\item\FoodSource;
use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\item\Durable;
use pocketmine\level\ChunkLoader;
use pocketmine\level\Explosion;
use pocketmine\level\format\FullChunk;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\WeakPosition;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\NoDynamicFieldsTrait;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\Network;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\AddHangingEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemPacket;
use pocketmine\network\mcpe\protocol\AddPaintingPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\BlockPickRequestPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\network\mcpe\protocol\ClientToServerHandshakePacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\CommandStepPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\DropItemPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\ExplodePacket;
use pocketmine\network\mcpe\protocol\FullChunkDataPacket;
use pocketmine\network\mcpe\protocol\HurtArmorPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryActionPacket;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\EntityFallPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\RemoveBlockPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\ReplaceItemInSlotPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\RiderJumpPacket;
use pocketmine\network\mcpe\protocol\ServerToClientHandshakePacket;
use pocketmine\network\mcpe\protocol\SetCommandsEnabledPacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\network\mcpe\protocol\SetHealthPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\ShowCreditsPacket;
use pocketmine\network\mcpe\protocol\SpawnExperienceOrbPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\network\mcpe\protocol\TakeItemEntityPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\network\mcpe\protocol\UnknownPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\network\mcpe\protocol\UseItemPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\SourceInterface;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\tile\ItemFrame;
use pocketmine\tile\Sign;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\Binary;
use pocketmine\utils\Config;
use pocketmine\utils\Color;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;
use pocketmine\Player;
use pocketmine\Server;

use HayaoPVE\main;

class MobSpawn extends Task{

	function __construct(PluginBase $owner){
		$this->owner = $owner;
	}

	function onRun(int $currentTick){
		foreach ($this->owner->config['entity']->getAll() as $key => $data){
			$cname = $data["name"];
			if($this->owner->$cname < $data["maxspawn"] and isset($data["x"])){//Mobの上限
				$eid = mt_rand(100000, 10000000);
				$yaw = mt_rand(0,359);
				if(isset($this->owner->LastAttackPlayer[$eid])){
					unset($this->owner->LastAttackPlayer[$eid]);
				}
				$rr = $data["randomspawn"];
				if($rr > 0){
					$randx = mt_rand(0,$rr);
					$randz = mt_rand(0,$rr);
					$x = $data["x"] - $rr/2 + $randx;
					$y = $data["y"];
					$z = $data["z"] - $rr/2 +$randz;
				}else{
					$x = $data["x"];
					$y = $data["y"];
					$z = $data["z"];
				}
				$type = $data["type"];
				if($type === "無"){
					$color = "";
				}elseif($type === "火"){
					$color = "§c";
				}elseif($type === "水"){
					$color = "§b";
				}elseif($type === "木"){
					$color = "§a";
				}elseif($type === "光"){
					$color = "§e";
				}elseif($type === "闇"){
					$color = "§6";
				}
				$itemid = $data["itemid"];
				if(!isset($data["koteilevel"])){
					$level2 = $data["level"];
					if($level2 === 1){
						$level = mt_rand(1, 4);
					}elseif($level2 === 2){
						$level = mt_rand(5, 8);
					}elseif($level2 === 3){
						$level = mt_rand(9, 12);
					}elseif($level2 === 4){
						$level = mt_rand(6,8);
					}elseif($level2 === 5){
						$level = mt_rand(1,3);
					}else{
						$level = 1;
					}
				}else{
					$level = $data["koteilevel"];
				}
				$mobname = $cname;
				$mobname = "§l§5Lv. ".$level."§r§l ".$mobname." §r§l(".$color."".$type."§r§l)";
				$level3 = "1.".$level;
				$pk = new AddPlayerPacket();
				$pk->entityRuntimeId = $eid;
				$pk->uuid = UUID::fromRandom();
				$pk->username = $mobname;
				/*$pk->x = $x;
				$pk->y = $y;
				$pk->z = $z;*/
				$pk->position = new Vector3($x, $y, $z);
				$pk->speedX = 0;
				$pk->speedY = 0;
				$pk->speedZ = 0;
				$pk->yaw = $yaw;
				$pk->pitch = 0;
				$pk->item = Item::get(intval($itemid),0,1);
				@$flags |= 0 << Entity::DATA_FLAG_INVISIBLE;
				@$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
				@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
				@$flags |= 0 << Entity::DATA_FLAG_IMMOBILE;
			       	$pk->metadata = [
					Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
						Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, "§l".$mobname." §r§l(".$color."".$type."§r§l)"],
					  	Entity::DATA_FLAG_NO_AI => [Entity::DATA_TYPE_BYTE, 1],
					  	Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
						Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, $data["size"]],//大きさ
					  	];
				$geometryJsonEncoded = base64_decode($data["geometrydata"]);
				if($geometryJsonEncoded !== ""){
					$geometryJsonEncoded = \json_encode(\json_decode($geometryJsonEncoded));
				}
		 		$skin = new Skin(base64_decode($data["skinid"]), base64_decode($data["skindata"]), base64_decode($data["capedata"]), base64_decode($data["geometryname"]), $geometryJsonEncoded);
		 		$xbox = mt_rand(100000, 1000000000);
				$this->owner->getServer()->updatePlayerListData($pk->uuid, $pk->entityRuntimeId, $mobname, $skin, $xbox, $this->owner->getServer()->getOnlinePlayers());
				foreach($this->owner->getServer()->getOnlinePlayers() as $player){
					$player->dataPacket($pk);
					$pk2 = new MobEquipmentPacket();
					$pk2->entityRuntimeId = $eid;
					$pk2->item = Item::get(intval($itemid),0,1);
					$pk2->inventorySlot = 0;
					$pk2->hotbarSlot = 0;
					$player->dataPacket($pk2);//Item
				}
				$this->owner->$cname = $this->owner->$cname + 1;
				$this->owner->entity[$eid]["name"] = $cname;
				$this->owner->entity[$eid]["target"] = "";
				$this->owner->entity[$eid]["item"] = $itemid;
				$this->owner->entity[$eid]["maxhp"] = $data["maxhp"] * $level3;
				$this->owner->entity[$eid]["hp"] = $data["maxhp"] * $level3;
				$this->owner->entity[$eid]["atk"] = $data["atk"] * $level3-0.2;
				$this->owner->entity[$eid]["def"] = $data["def"] * $level-0.2;
				$this->owner->entity[$eid]["atkrange"] = $data["atkrange"];
				$this->owner->entity[$eid]["atktime"] = 0;
				$this->owner->entity[$eid]["reatk"] = floor($data["reatk"] / $this->owner->roopspeed);
				$this->owner->entity[$eid]["speed"] = $data["speed"] * $this->owner->roopspeed;
				$this->owner->entity[$eid]["x"] = $x;
				$this->owner->entity[$eid]["y"] = $y;
				$this->owner->entity[$eid]["z"] = $z;
				$this->owner->entity[$eid]["yaw"] = $yaw;
				$this->owner->entity[$eid]["pitch"] = 0;
				$this->owner->entity[$eid]["move"] = 0;
				$this->owner->entity[$eid]["uuid"] = $pk->uuid;
				$this->owner->entity[$eid]["xbox"] = $xbox;
				$this->owner->entity[$eid]["skin"] = $skin;
				$this->owner->entity[$eid]["size"] = $data["size"];
				$this->owner->entity[$eid]["damage"] = "true";
				$this->owner->entity[$eid]["level"] = $level;
				$this->owner->entity[$eid]["change"] = 0;
				if(isset($data["plusY"])){
					$this->owner->entity[$eid]["plusY"] = $data["plusY"];
				}
				if(isset($data["type"])){
					$this->owner->entity[$eid]["type"] = $data["type"];					
				}else{
					$this->owner->entity[$eid]["type"] = "無";										
				}
				if(isset($data["exp"])){
					$this->owner->entity[$eid]["exp"] = $data["exp"] * $level3;
				}
				if(isset($data["gold"])){
					$this->owner->entity[$eid]["gold"] = $data["gold"] * $level3;
				}
				if(isset($data["drop1"])){
					$this->owner->entity[$eid]["drop1"] = $data["drop1"];
					$this->owner->entity[$eid]["dropkakuritu1"] = $data["dropkakuritu1"];
				}
				if(isset($data["drop2"])){
					$this->owner->entity[$eid]["drop2"] = $data["drop2"];
					$this->owner->entity[$eid]["dropkakuritu2"] = $data["dropkakuritu2"];
				}
				if(isset($data["drop3"])){
					$this->owner->entity[$eid]["drop3"] = $data["drop3"];
					$this->owner->entity[$eid]["dropkakuritu3"] = $data["dropkakuritu3"];
				}
				if(isset($data["drop4"])){
					$this->owner->entity[$eid]["drop4"] = $data["drop4"];
					$this->owner->entity[$eid]["dropkakuritu4"] = $data["dropkakuritu4"];
				}
				if(isset($data["drop5"])){
					$this->owner->entity[$eid]["drop5"] = $data["drop5"];
					$this->owner->entity[$eid]["dropkakuritu5"] = $data["dropkakuritu5"];
				}
				if(isset($data["drop6"])){
					$this->owner->entity[$eid]["drop6"] = $data["drop6"];
					$this->owner->entity[$eid]["dropkakuritu6"] = $data["dropkakuritu6"];
				}
				if(isset($data["drop7"])){
					$this->owner->entity[$eid]["drop7"] = $data["drop7"];
					$this->owner->entity[$eid]["dropkakuritu7"] = $data["dropkakuritu7"];
				}
				if(isset($data["drop8"])){
					$this->owner->entity[$eid]["drop8"] = $data["drop8"];
					$this->owner->entity[$eid]["dropkakuritu8"] = $data["dropkakuritu8"];
				}
				if(isset($data["drop9"])){
					$this->owner->entity[$eid]["drop9"] = $data["drop9"];
					$this->owner->entity[$eid]["dropkakuritu9"] = $data["dropkakuritu9"];
				}
				if(isset($data["drop10"])){
					$this->owner->entity[$eid]["drop10"] = $data["drop10"];
					$this->owner->entity[$eid]["dropkakuritu10"] = $data["dropkakuritu10"];
				}
				if(isset($data["searchdistance"])){
					$this->owner->entity[$eid]["searchdistance"] = $data["searchdistance"];
				}else{
					$this->owner->entity[$eid]["searchdistance"] = 32;
				}
				if(isset($data["playerdistance"])){
					$this->owner->entity[$eid]["playerdistance"] = $data["playerdistance"];
				}else{
					$this->owner->entity[$eid]["playerdistance"] = 2;
				}
				if(isset($data["boss"])){
					$this->owner->entity[$eid]["boss"] = $data["boss"];
				}
				$this->owner->eid[$eid] = $eid;
			}
		}
	}
}