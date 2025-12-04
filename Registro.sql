CREATE TABLE public.Registro (
  ID_Registro bigint GENERATED ALWAYS AS IDENTITY NOT NULL UNIQUE,
  ID_Sensor bigint NOT NULL,
  MomentoCaptura timestamp with time zone NOT NULL,
  MomentoRegistro timestamp with time zone NOT NULL DEFAULT now(),
  UmidAr bigint NOT NULL,
  TempAr double precision NOT NULL,
  UmidSolo bigint NOT NULL,
  Chuva bigint NOT NULL,
  Luminosidade bigint NOT NULL,
  fk_Horta bigint NOT NULL,
  CONSTRAINT Registro_pkey PRIMARY KEY (ID_Registro),
  CONSTRAINT Registro_fk_Horta_fkey FOREIGN KEY (fk_Horta) REFERENCES public.Horta(ID_Horta)
);