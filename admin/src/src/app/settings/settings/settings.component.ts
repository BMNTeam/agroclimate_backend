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
        this.srv.settings.subscribe( i => this.settings = i);
    }

    save() {
        this.srv.set(this.settings);
    }


}
