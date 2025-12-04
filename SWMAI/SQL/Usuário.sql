-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.


CREATE TABLE public.Usuário (
  ID_Usuário bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  Nome text NOT NULL,
  Senha text NOT NULL,
  TipoPessoa text NOT NULL,
  DataNasc timestamp without time zone NOT NULL,
  Email text NOT NULL UNIQUE,
  CPF text NOT NULL UNIQUE,
  CNPJ text NOT NULL UNIQUE,
  Telefone bigint NOT NULL,
  NivelAcesso bigint NOT NULL,
  CONSTRAINT Usuário_pkey PRIMARY KEY (ID_Usuário)
);