<?php
class Equipo {
	private $id;
	private $pos;
	private $equip;
	private $user_id;
	private $escudo;
	private $jugados;
	private $ganados;
	private $empatados;
	private $perdidos;
	private $puntos;
	private $gf_gc;

	public function __construct($id, $pos, $equip, $user_id, $escudo, $jugados, $ganados, $empatados, $perdidos, $puntos, $gf_gc) {
		$this->id = $id;
		$this->pos = $pos;
		$this->equip = $equip;
		$this->user_id = $user_id;
		$this->escudo = $escudo;
		$this->jugados = $jugados;
		$this->ganados = $ganados;
		$this->empatados = $empatados;
		$this->perdidos = $perdidos;
		$this->puntos = $puntos;
		$this->gf_gc = $gf_gc;
	}

	public function getId() { return $this->id; }
	public function setId($id) { $this->id = $id; }

	public function getPos() { return $this->pos; }
	public function setPos($pos) { $this->pos = $pos; }

	public function getEquip() { return $this->equip; }
	public function setEquip($equip) { $this->equip = $equip; }

	public function getUserId() { return $this->user_id; }
	public function setUserId($user_id) { $this->user_id = $user_id; }

	public function getEscudo() { return $this->escudo; }
	public function setEscudo($escudo) { $this->escudo = $escudo; }

	public function getJugados() { return $this->jugados; }
	public function setJugados($jugados) { $this->jugados = $jugados; }

	public function getGanados() { return $this->ganados; }
	public function setGanados($ganados) { $this->ganados = $ganados; }

	public function getEmpatados() { return $this->empatados; }
	public function setEmpatados($empatados) { $this->empatados = $empatados; }

	public function getPerdidos() { return $this->perdidos; }
	public function setPerdidos($perdidos) { $this->perdidos = $perdidos; }

	public function getPuntos() { return $this->puntos; }
	public function setPuntos($puntos) { $this->puntos = $puntos; }

	public function getGfGc() { return $this->gf_gc; }
	public function setGfGc($gf_gc) { $this->gf_gc = $gf_gc; }
}