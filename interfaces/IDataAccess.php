<?php
interface IDataAccess{
    public function Create();
    public function Get();
    public function GetById();
    public function Update();
    public function Delete();
}