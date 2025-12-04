
CREATE TABLE public.Horta (
  ID_Horta bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  NomeHorta text NOT NULL,
  Localização text,
  TipoDoPlantio text NOT NULL,
  fk_Usuario bigint NOT NULL,
  CONSTRAINT Horta_pkey PRIMARY KEY (ID_Horta),
  CONSTRAINT Horta_fk_Usuario_fkey FOREIGN KEY (fk_Usuario) REFERENCES public.Usuário(ID_Usuário)
);