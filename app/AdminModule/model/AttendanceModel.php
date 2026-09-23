<?php

declare(strict_types = 1);

namespace Admin\Model;

use Admin\Dial\AttendanceTypeDial;
use Nette\Database\Explorer;
use Nette\Utils\DateTime;

class AttendanceModel
{
	public function __construct
	(
		private readonly Explorer $Database
	)
	{
	}


	/**
	 * Doplní docházku "přijdu" do budoucích termínů, kde hráč ještě nic nemá
	 */
	public function prefillPlayer(int $playerId): void
	{
		$termIdArray = $this->Database->table('term')
			->where('date >= ?', (new DateTime)->format('Y-m-d'))
			->where('available', 1)
			->where('id NOT IN ?', $this->Database->table('attendance')->select('term_id')->where('player_id', $playerId))
			->fetchPairs(null, 'id');

		foreach($termIdArray as $termId)
		{
			$this->insertYes($playerId, $termId);
		}
	}


	/**
	 * Doplní docházku "přijdu" všem aktivním hráčům s předvyplněním
	 */
	public function prefillTerm(int $termId): void
	{
		$playerIdArray = $this->Database->table('player')
			->where('prefill', 1)
			->where('active', 1)
			->where('id NOT IN ?', $this->Database->table('attendance')->select('player_id')->where('term_id', $termId))
			->fetchPairs(null, 'id');

		foreach($playerIdArray as $playerId)
		{
			$this->insertYes($playerId, $termId);
		}
	}


	/**
	 * Zruší docházku "přijdu" v budoucích termínech
	 */
	public function clearPlayer(int $playerId): void
	{
		$termIdArray = $this->Database->table('term')
			->where('date >= ?', (new DateTime)->format('Y-m-d'))
			->fetchPairs(null, 'id');

		if(!$termIdArray)
		{
			return;
		}

		$this->Database->table('attendance')
			->where('player_id', $playerId)
			->where('term_id', $termIdArray)
			->where('type', AttendanceTypeDial::YES)
			->delete();
	}


	private function insertYes(int $playerId, int $termId): void
	{
		$this->Database->table('attendance')
			->insert([
				'player_id' => $playerId,
				'term_id' => $termId,
				'type' => AttendanceTypeDial::YES,
			]);
	}
}
