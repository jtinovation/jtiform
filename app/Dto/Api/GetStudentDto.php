<?php

namespace App\Dto\Api;

class GetStudentDto
{
  public string $id;
  public string $nim;
  public string $name;
  public int $generation;
  public StudyProgramDto $studyProgram;
  public MajorDto $major;

  public function __construct(
    string $id,
    string $nim,
    string $name,
    int $generation,
    StudyProgramDto $studyProgram,
    MajorDto $major
  ) {
    $this->id = $id;
    $this->nim = $nim;
    $this->name = $name;
    $this->generation = $generation;
    $this->studyProgram = $studyProgram;
    $this->major = $major;
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['id'],
      $data['nim'],
      $data['name'],
      $data['generation'],
      new StudyProgramDto(
        $data['study_program']['id'],
        $data['study_program']['name']
      ),
      new MajorDto(
        $data['major']['id'],
        $data['major']['name']
      )
    );
  }
}

class StudyProgramDto
{
  public string $id;
  public string $name;

  public function __construct(string $id, string $name)
  {
    $this->id = $id;
    $this->name = $name;
  }
}

class MajorDto
{
  public string $id;
  public string $name;

  public function __construct(string $id, string $name)
  {
    $this->id = $id;
    $this->name = $name;
  }
}
