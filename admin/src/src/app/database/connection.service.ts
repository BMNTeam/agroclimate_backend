import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../environments/environment";
import {Observable} from "rxjs/Observable";


export interface Meteostation {
    ID: number | string,
    Name: string
}

@Injectable()
export class ConnectionService {

    constructor(private http: HttpClient) {
    }

    public getMeteostationsList(param: string): Observable<Meteostation[]>
    {
        return this.http.get<Meteostation[]>(environment.URL + 'routes/meteostations.php' + `?${param}`);
    }

    public getTP(data: any): Observable <any>
    {
        let params = data;
        return this.http.get(environment.URL + 'routes/climate_tp.php', {
            params: params
        });
    }
}
