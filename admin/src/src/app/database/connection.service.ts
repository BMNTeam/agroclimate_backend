import {Injectable} from '@angular/core';
import {HttpClient, HttpParams} from "@angular/common/http";
import {environment} from "../../environments/environment";
import {Observable} from "rxjs/Observable";
import {Subject} from "rxjs/Subject";
import {EditRequest} from "./edit-data/edit-data.component";

export interface Meteostation {
    ID: number | string,
    Name: string
}

export interface DecadesGetParams {
    yearStart: number,
    meteostationId: number

}

@Injectable()
export class ConnectionService {
    meteostations: Subject<Meteostation[]>;

    constructor(private http: HttpClient) {
        this.meteostations = new Subject<Meteostation[]>();
        this.setMeteostations();
    }

    public getMeteostationsList(param: string): Observable<Meteostation[]>
    {
        return this.http.get<Meteostation[]>(environment.URL + 'routes/meteostations.php' + `?${param}`);
    }

    private setMeteostations(): void
    {
        this.getMeteostationsList('all').subscribe(
            res => this.meteostations.next(res)
        )
    }

    public saveDecadesData(save: EditRequest): Observable<EditRequest>
    {
        let headers = {
            'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'
        };
        return this.http.post<EditRequest>(environment.URL + "routes/decades_tp.php", {save}, {headers})
    }

    public getDecadesData(reqParams: DecadesGetParams)
    {
        let params = {
            yearStart: reqParams.yearStart.toString(),
            meteostationId: reqParams.meteostationId.toString()
        };
        return this.http.get(environment.URL + 'routes/decades_tp.php?mode=edit', {params})
    }

    public getTP(data: any): Observable <any>
    {
        let params = data;
        return this.http.get(environment.URL + 'routes/climate_tp.php', {
            params: params
        });
    }
}
