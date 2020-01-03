var snow = {

    wind : 0,
    maxXrange : 100,
    minXrange : 10,
    maxSpeed : 2,
    minSpeed : 1,
    color : "#fff",
    char : "*",
    maxSize : 20,
    minSize : 8,

    flakes : [],
    WIDTH : 0,
    HEIGHT : 0,

    init : function(nb){
        var o = this,
            frag = document.createDocumentFragment();
        o.getSize();



        for(var i = 0; i < nb; i++){
            var flake = {
                x : o.random(o.WIDTH),
                y : - o.maxSize,
                xrange : o.minXrange + o.random(o.maxXrange - o.minXrange),
                yspeed : o.minSpeed + o.random(o.maxSpeed - o.minSpeed, 100),
                life : 0,
                size : o.minSize + o.random(o.maxSize - o.minSize),
                html : document.createElement("span")
            };

            flake.html.style.position = "absolute";
            flake.html.style.top = flake.y + "px";
            flake.html.style.left = flake.x + "px";
            flake.html.style.fontSize = flake.size + "px";
            flake.html.style.color = o.color;
            flake.html.appendChild(document.createTextNode(o.char));

            frag.appendChild(flake.html);
            o.flakes.push(flake);
        }

        document.body.appendChild(frag);
        o.animate();

        window.onresize = function(){o.getSize();};
    },

    animate : function(){
        var o = this;
        for(var i = 0, c = o.flakes.length; i < c; i++){
            var flake = o.flakes[i],
                top = flake.y + flake.yspeed,
                left = flake.x + Math.sin(flake.life) * flake.xrange + o.wind;
            if(top < o.HEIGHT - flake.size - 10 && left < o.WIDTH - flake.size && left > 0){
                flake.html.style.top = top + "px";
                flake.html.style.left = left + "px";
                flake.y = top;
                flake.x += o.wind;
                flake.life+= .01;
            }
            else {
                flake.html.style.top = -o.maxSize + "px";
                flake.x = o.random(o.WIDTH);
                flake.y = -o.maxSize;
                flake.html.style.left = flake.x + "px";
                flake.life = 0;
            }
        }
        setTimeout(function(){
            o.animate();
        },20);
    },

    random : function(range, num){
        var num = num?num:1;
        return Math.floor(Math.random() * (range + 1) * num) / num;
    },

    getSize : function(){
        this.WIDTH = document.body.clientWidth || window.innerWidth;
        this.HEIGHT = document.body.clientHeight || window.innerHeight;
    }

};