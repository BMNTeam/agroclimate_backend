import {Component, OnInit} from '@angular/core';
import {SettingsService, Settings} from "../settings.service";


@Component({
    selector: 'app-settings',
    templateUrl: './settings.component.html',
    styleUrls: ['./settings.component.sass'],
    providers: [SettingsService]
})
export class SettingsComponent implements OnInit {

    public settings: Settings;

    constructor(private srv: SettingsService) {
    }

    ngOnInit() {
        this.srv.settings.subscribe(i => {
                this.settings = Object.keys(i)
                    .reduce((r, c) => {
                        r[c] = !!+i[c];// by default receives strings which always consider as true
                        return r;

                    }, {} as Settings);
            }
        )
    }

    save() {
        this.srv.set(this.settings);
    }


}
