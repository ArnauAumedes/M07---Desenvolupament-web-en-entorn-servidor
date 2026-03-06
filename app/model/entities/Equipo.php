<?php
class Equipo
{
	private int|string|null $id;
	private string $equip;
	private ?int $entrenador;
	private string $escudo;
	private int $jugados;
	private int $ganados;
	private int $empatados;
	private int $perdidos;
	private int $objetivo;
	private ?int $creadorId;

	public function __construct(int|string|null $id, string $equip, ?int $entrenador, string $escudo, int $jugados, int $ganados, int $empatados, int $perdidos, int $objetivo, ?int $creadorId = null)
	{
		$this->id = $id;
		$this->equip = $equip;
		$this->entrenador = $entrenador;
		$this->escudo = $escudo;
		$this->jugados = $jugados;
		$this->ganados = $ganados;
		$this->empatados = $empatados;
		$this->perdidos = $perdidos;
		$this->objetivo = $objetivo;
		$this->creadorId = $creadorId;
	}

	public function getId()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
	}


	public function getEquip()
	{
		return $this->equip;
	}
	public function setEquip($equip)
	{
		$this->equip = $equip;
	}

	public function getEntrenador()
	{
		return $this->entrenador;
	}
	public function setEntrenador($entrenador)
	{
		$this->entrenador = $entrenador;
	}

	public function getEscudo()
	{
		return $this->escudo;
	}
	public function setEscudo($escudo)
	{
		$this->escudo = $escudo;
	}

	public function getJugados()
	{
		return $this->jugados;
	}
	public function setJugados($jugados)
	{
		$this->jugados = $jugados;
	}

	public function getGanados()
	{
		return $this->ganados;
	}
	public function setGanados($ganados)
	{
		$this->ganados = $ganados;
	}

	public function getEmpatados()
	{
		return $this->empatados;
	}
	public function setEmpatados($empatados)
	{
		$this->empatados = $empatados;
	}

	public function getPerdidos()
	{
		return $this->perdidos;
	}
	public function setPerdidos($perdidos)
	{
		$this->perdidos = $perdidos;
	}
	public function getObjetivo()
	{
		return $this->objetivo;
	}
	public function setObjetivo($objetivo)
	{
		$this->objetivo = $objetivo;
	}

	public function getCreadorId()
	{
		return $this->creadorId;
	}
	public function setCreadorId($creadorId)
	{
		$this->creadorId = $creadorId;
	}
}