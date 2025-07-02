# Esercitazione Pimcore 11

#### **Descrizione:**

1. **Importazione da CSV:**
   
   - Creare un comando Symfony (`bin/console app:import-cars path/al/file.csv`) che importi un file CSV contenente informazioni di automobili da salvare in Pimcore.
   
   - La prima riga del CSV contiene l'header da cui è possibile recuperare tutte informazioni degli oggetti.
   
   - Il candidato dovrà creare il DataObject in Pimcore più strutturato possibile per rappresentare le informazioni contenute all'interno del CSV.
   
   - Durante l'import, gestire duplicati (aggiornare l'oggetto esistente se è già stato importato in precedenza) e loggare eventuali errori o anomalie.
     
     - **[NiceToHave]** Utilizzare l'ApplicationLogger di Pimcore per memorizzare gli errori o le anomalie.

2. **API di dettaglio:**
   
   - Creare un endpoint API REST (`/api/models/{id}`) che restituisca i dettagli di un oggetto, serializzando i dati in formato JSON.
     
     - Ovviamente non è obbligatorio inserire TUTTI i dati, ma almeno quelli principali e qualcuno di esempio.

3. **API di ricerca:**
   
   - Creare un endpoint API REST (`/api/models/search?query=...`) che cerchi sugli oggetti in base a una ricerca *contains* sul campo che sul CSV è indicato come `Model`.
   
   - L'API dovrà restituire un elenco di risultati contenente solo informazioni di base (es. `id`, `name`, `model`).

---

#### **Requisiti tecnici:**

- Utilizzare Pimcore 11.

- Usare le best practice di Symfony per Command, Controller, Dependency Injection e Routing.

- Utilizzare i DataObject di Pimcore per modellare i dati.

- Scrivere codice chiaro e mantenibile, con adeguati commenti e logging dove necessario.

---

#### **Consegna:**

- Repository Git (GitHub o altro) o file ZIP con il codice completo.

- Eventuale README con breve spiegazione delle scelte fatte, requisiti di ambiente e istruzioni di esecuzione (installazione, import, test delle API).


---
---
---


### Installazione

1. Dopo aver clonato o effettuato il fork della repository, si potrà procedere alla creazione dei container docker lanciando il comando: `docker compose up -d` assicurandoci di mantenere libera la porta `80`.

2. Non appena tutti i container docker saranno online, procedere con l'installazione delle dipendenze composer lanciando il comando: `docker compose exec php composer install` o `docker compose exec php sh` e successivamente `composer install`.

3. Lanciare il comando: 
```bash
docker compose exec php vendor/bin/pimcore-install --mysql-host-socket=db --mysql-username=pimcore --mysql-password=pimcore --mysql-database=pimcore
```
 - inserite i vostri dati, Username, Password
 - Alla richiesta di installazione dei bundle usate "yes"
 - installate i seguenti bundle:
    - 0 PimcoreApplicationLoggerBundle
    - 4 PimcoreSimpleBackendSearchBundle
    - 5 PimcoreStaticRoutesBundle
    - Usate di nuovo "yes" per installare correttamente i bundle selezionati

4. Il set-up è completo, potete procedere alla pagina <http://localhost> o al pannello admin <http://localhost/admin> utilizzando Username e Password inseriti dopo aver lanciato il comando del passaggio #3.

---

### Comandi

1. Importazione da CSV, `docker compose exec php bin/console app:import-cars ./public/data/all-vehicles-modelpublic.csv` 
    - Questo file eseguirà l'import di tutti gli elementi presenti nel file CSV seguendo il layout dei dataObjects già creati precedentemente nel pannello admin.
        - Il comando prima si assicura che il file esista effettivamente, e che possa leggere il formato CSV, successivamente apre uno stream in lettura per il file.
        - Sono stati creati più normalizer, il primo principale per rimuovere tutti gli spazi vuoti, ad inizio e fine di ogni valore, successivamnte di Capitalizzare ogni lettera dopo uno spazio, rimozione del BOM character presente in `make`, ed infine rimozione di ogni spaziatura e carattere `-`. 
        Il secondo normalizer è stato creato per gli elementi numerici, per cambiare il dataType da stringa a numerico, impostando la condizione `true` ad ogni elemento non presente nella FieldsNumerica (Che in realtà è una fields di type string), se il valore non è presente all'interno della field, il valore corrispondente a quella key verrà trasformato in valore numerico.
        - Ho creato una classe Cars al namespace `App\DataObjects`, che crea un costruttore che accetta come parametri due array, $data e $header, informazioni indispensabili per l'importazione dei valori all'interno degli object pimcore, che riesce a gestire le duplicazioni e logiche di base.
    - N.B: Il file è già presente a quel PATH, ma in caso di file diverso, basterà inserire un path nuovo verso il nuovo file CSV.
 
2. Delete di tutti gli elementi presenti all'interno del dataObject Car.
    - Questo comando non è stato richiesto secondo la consegna del test, ma è stata una mia implementazione rapida per la cancellazione di tutti i file precedentemente importati in pimcore, ma con dei dati che non combaciavano, era una specie di "Reset" che utilizzavo a volte.


### Controller

1. La prima API GET punta all'indirizzo `http://localhost/api/models/{id}`, viene utilizzato per  estrarre i dati secondo un ID specifico e ritornare un semplice JSON serializzato, passando i dati estratti da CarDataRepository::getCar() che si occupa di serializzare correttamente i dati del dataObject Car

2. La seconda API GET punta all'indirizzo `http://localhost/api/models/search?{params}={model}` in questa API sarà richiesto di estrarre i dati secondo uno specifico dato, estraibile dalla richiesta inviandolo inserendo il parametro "query"
    - EX: `http://localhost/api/models/search?query=B2200/B2600i`
Questa API avrà lo stesso comportamento della prima, ma restituendo un elenco di risultati.

### Dettagli aggiuntivi

- E' stato implementato l'`ApplicationLogger` sia sulle rotte che sui comandi, in alcuni casi ho deciso di utilizzarlo come "debug" di info, per confrontare il time-stamp di inizio e fine import ad esempio. Funziona correttamente anche per gli errori, ed è stato immediatamente utile per risolvere un errore improvviso 
