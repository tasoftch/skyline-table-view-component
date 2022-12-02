/*
 * BSD 3-Clause License
 *
 * Copyright (c) 2022, TASoft Applications
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 *  Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

import {Plugin} from "../core/plugin";

import $ from "../utils/jquery";

export class InlineTableRenderPlugin extends Plugin {
    get name() {
        return "cell-render";
    }

    install(context, options) {
        this.tableView = context;
        this.templates = new Map();

        const self = this;

        $(context.el).find("tr.template").each(function() {
            let tplName = $(this).attr("data-tpl");
            if(!tplName)
                tplName = "@default";

            if(!self.default_template || tplName === '@default')
                self.default_template = tplName;

            if(self.templates.has( tplName ))
                throw new Error("Template "+tplName+" already exists");

            self.templates.set(tplName, $(this).html().trim());
        }).remove();

        context.on("renderrow", ({row, index, data, meta}) => {
            let tpl = meta.template;
            if(!tpl || !this.templates.has(tpl))
                tpl = this.default_template;


            row.append( this.templates.get(tpl) );

            $(row).find("td[data-ref]").each(function() {
                let value = $(this).attr("data-ref");
                if(!value || value.length < 1)
                    throw new Error("No data-ref attribute provided");

                value = data[value];

                const fmt = $(this).attr("data-format");
                if(fmt && Skyline && Skyline.String)
                    value = Skyline.String.format(fmt, value, $(this));

                $(this).html(value);
            })
        })
    }
}