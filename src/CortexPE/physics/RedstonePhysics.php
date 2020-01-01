<?php
/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author larryTheCoder
 * @link https://CortexPE.xyz
 */

namespace CortexPE\physics;

use CortexPE\Main;
use CortexPE\physics\thread\RedstoneChecks;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\event\Listener;
use pocketmine\level\format\Chunk;
use pocketmine\Server;

/**
 * Internal physics checks.
 * The theory is to run and check redstone within the area. The check is run inside a new thread,
 * given a thread such data as Chunks information also with some data which is encoded in json.
 *
 * @package CortexPE\physics
 * @author larryTheCoder
 */
class RedstonePhysics implements Listener {

	private $searchDepth = 1.5;

	/** @var RedstoneChecks|null */
	private $redstoneCheck = null; // Threads where the checks are passed

	/** @var Chunk[] */
	private $checkArea = [];
	/** @var Block[] */
	private $blockArea = [];

	public function __construct(){
		Server::getInstance()->getPluginManager()->registerEvents($this, Main::getInstance());
	}

	/**
	 * Chunk loads listener were supposed to checks if an element 'redstone' exists
	 * within this chunk area.
	 *
	 * @priority MONITOR
	 * @param ChunkLoadEvent $event
	 */
	public function onChunkLoad(ChunkLoadEvent $event){
		$chunk = $event->getChunk();

		for($y = 0; $y <= 256; $y++) for($x = 0; $x <= 15; $x++) for($z = 0; $z <= 15; $z++){
			$id = $chunk->getBlockId($x, $y, $z);

			if(self::isRedstoneFamily($id)){
				$this->checkArea[] = $chunk;

				$this->checkRedstoneArea();
			}
		}
	}

	/**
	 * Checks partial parts of the block area.
	 *
	 * @priority MONITOR
	 * @param BlockPlaceEvent $block
	 */
	public function onBlockPlace(BlockPlaceEvent $block){
		if(!self::isRedstoneFamily($block->getBlock()->getId())){
			return;
		}

		$this->checkArea[] = $block->getBlock()->getLevel()->getChunkAtPosition($block->getBlock());

		$this->checkRedstoneArea();
	}

	/**
	 * Checks the area of
	 */
	private function checkRedstoneArea(){

	}

	private static function isRedstoneFamily($id){
		// TODO: Activator rails

		return $id == BlockIds::REDSTONE_BLOCK || $id == BlockIds::REDSTONE_LAMP || $id == BlockIds::REDSTONE_ORE ||
			$id == BlockIds::REDSTONE_TORCH || $id == BlockIds::REDSTONE_WIRE || self::isRedstoneEngine($id);
	}

	private static function isRedstoneEngine($id){

	}
}