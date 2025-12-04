<?php
// Arquivo: SWMAI/geral/index.php
session_start();
$pagina_atual = basename($_SERVER['PHP_SELF']);
require_once('header.php');

?>
<style>
    /* Estilos do banner e da caixa de acrílico */
    .banner {
        width: 100%;
        height: 400px;
        /* Altura do banner */
        background-image: url('../IMG/fundo.jpg');
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;

        /* Centraliza o conteúdo (a caixa de acrílico) */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .acrilico-box {
        /* Estilos para a caixa com efeito de acrílico */
        padding: 30px 40px;
        /* Espaçamento interno */
        background-color: rgba(255, 255, 255, 0.1);
        /* Fundo branco semi-transparente */
        backdrop-filter: blur(5px);
        /* Efeito de desfoque (blur) no fundo */
        -webkit-backdrop-filter: blur(10px);
        /* Para compatibilidade com navegadores Webkit */
        border: 1px solid rgba(255, 255, 255, 0.3);
        /* Borda sutil */
        border-radius: 15px;
        /* Cantos arredondados */
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        /* Sombra para profundidade */
        text-align: center;
    }

    .titulo-acrilico {
        /* Estilos para o texto do título */
        color: white;
        /* Cor do texto */
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        /* Sombra no texto para melhor contraste */
        margin: 0;
        /* Remove a margem padrão do h1 */
    }
</style>
<main>


    <section class="banner" id="aviso">
        <div class="acrilico-box">
            <h1 class="titulo-acrilico">Sistema Web De Monitoramento Agrícola Inteligente</h1>
        </div>
    </section>
    <div >
        <?php if (isset($_SESSION['index_message'])): ?>
            <div class="container mt-3">
                <div id="auto-fade-alert" class="alert alert-<?= $_SESSION['index_message']['type'] ?> alert-dismissible fade show text-center" role="alert">
                    <?= htmlspecialchars($_SESSION['index_message']['text']) ?>
                </div>
            </div>
            <?php unset($_SESSION['index_message']); ?>
        <?php endif; ?>
    </div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <p>O objetivo deste trabalho é desenvolver um sistema web de monitoramento agrícola que possibilite ao proprietário rural a análise e o gerenciamento eficiente de dados ambientais, tais como umidade do ar, umidade do solo, insolação, radiação ultravioleta (UV), entre outros parâmetros relevantes. O sistema proposto será acessível por meio de uma interface dinâmica e intuitiva, que registra e processa informações em tempo real, permitindo ao produtor manter a qualidade e a segurança de seu plantio com custos reduzidos.</p>
                <p>A operação do sistema se baseia em pontos receptores equipados com sensores, que coletam e transmitem dados para uma base central. Esses dados são processados e apresentados de forma gráfica e informativa, facilitando a interpretação e a tomada de decisões. Além disso, o sistema oferece recomendações personalizadas de acordo com o tipo de cultivo analisado. Por exemplo, no caso de um plantio de morangos, que é sensível à alta insolação, o sistema notificará o produtor sempre que houver uma incidência solar excessiva, permitindo que medidas preventivas sejam adotadas de maneira ágil.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Placeat architecto assumenda deleniti molestias, voluptatum incidunt odit. Labore tempora dolor explicabo consequatur cupiditate placeat in quaerat reiciendis quidem doloremque! Consequuntur, dicta.
                    Repellendus quam tempora facilis qui molestias laboriosam blanditiis unde minima explicabo voluptatibus illum nobis, omnis voluptatum, aliquam suscipit inventore commodi? Sequi dicta, beatae tenetur necessitatibus at aliquam nam aliquid debitis?
                    Quaerat, laudantium fuga officiis ut repellendus fugiat dolor tempore delectus enim voluptas ducimus pariatur ipsum voluptatem nam maxime! Quidem iste at veritatis fuga! Molestiae dignissimos, optio corporis inventore quam commodi!
                    Corporis, omnis dignissimos, accusamus sequi vel quae nobis porro odio hic et inventore. Rem dolore tenetur eveniet itaque corrupti quidem vel eum quos. Veniam consectetur vero est? Doloribus, sequi earum.
                    Iure harum aut repellendus expedita fugit vero repudiandae asperiores dignissimos, illum nulla, cum dicta debitis voluptatem reprehenderit. Aliquid numquam ex accusantium nesciunt voluptatum at, iure rem quisquam? Nostrum, excepturi aliquid!
                    Laudantium quos consequuntur aliquam voluptatem sed natus eum tempore aut praesentium exercitationem. Voluptate soluta voluptatibus, necessitatibus culpa quae a nostrum magni nulla commodi, vel totam ex similique, alias asperiores quam.
                    Cumque, voluptates ea qui, eius praesentium ducimus, dolore perferendis rem similique at aliquam minima? Est, ipsum. Accusamus, facilis magnam, debitis blanditiis quibusdam recusandae dolor optio sed molestias odio nisi fugit!
                    Quo quaerat quasi natus dolores excepturi itaque, consequuntur pariatur asperiores harum commodi dolorem saepe ad, similique reiciendis architecto ut, alias veritatis ipsam doloremque omnis error perspiciatis dolore. Repellendus, laboriosam incidunt?
                    Tempore iusto veritatis ipsa suscipit consequuntur ad possimus mollitia, voluptatem nisi voluptate consequatur aperiam! Quod blanditiis enim inventore voluptatem distinctio! Non quos ratione rerum necessitatibus. Fuga cum soluta voluptates quis.
                    Suscipit perferendis quas repellendus consequatur laudantium adipisci, laboriosam maxime nemo iusto eveniet necessitatibus illum sed neque earum officia dolorem temporibus minus numquam tenetur? Temporibus, magni? Tempora nisi corporis error animi?
                    Nemo a, molestiae odit laborum deleniti cumque deserunt praesentium quo earum maiores quod fugiat, labore officiis nulla saepe atque vero perspiciatis expedita obcaecati et magni, quasi nihil. Blanditiis, repellat quo.
                    Architecto dicta error perspiciatis! Eligendi animi vero autem vel eos libero dicta praesentium error magnam quidem laboriosam nesciunt, optio necessitatibus ipsa numquam tenetur culpa atque eveniet. Modi, nemo. Sapiente, voluptas!
                    Reiciendis quod ipsum esse vel dolore nam quisquam suscipit ut modi, animi, libero ab. Doloremque adipisci delectus sunt asperiores suscipit aliquam nisi, perspiciatis, hic eos dolor voluptas eveniet itaque eum.
                    Voluptate facere, exercitationem expedita earum minus similique nisi assumenda iste natus tenetur fugit possimus praesentium ut eligendi distinctio reiciendis pariatur nesciunt debitis necessitatibus commodi doloremque eius soluta, qui laudantium? Vel.
                    Doloremque officiis earum exercitationem voluptatum velit, fugiat, facere quod debitis, corporis natus harum voluptatem hic perferendis recusandae voluptatibus dolorem numquam illum quas. Voluptas iste earum ut deleniti sit quia qui?
                    Quae in repudiandae soluta voluptatibus vitae rerum ex! Illo ipsa at minus aliquam commodi corrupti dignissimos fugiat numquam nulla delectus nihil ex eveniet, error, hic repellat! Autem provident totam similique.
                    Harum nobis reiciendis molestiae illo saepe doloremque laborum. Perspiciatis, at non commodi a ab ratione, vel placeat nisi architecto ipsam neque eum temporibus qui eligendi inventore natus voluptatum, quaerat deleniti?
                    A id dolorem corporis error dolore maxime nostrum impedit velit, magnam reprehenderit doloribus exercitationem sunt amet quas iusto sed libero minus. Eius nisi sint delectus qui, consequuntur repudiandae. Ipsam, consequatur?
                    Ad temporibus nam, explicabo exercitationem similique repellendus commodi quod maiores aliquid non quas optio autem unde numquam! Qui, similique laboriosam deserunt numquam tenetur repellat beatae sunt asperiores reprehenderit sequi delectus.
                    Dignissimos pariatur recusandae architecto, omnis doloribus itaque a maxime quam exercitationem sed eum totam inventore numquam nisi porro ex tempore obcaecati et ipsum optio sequi asperiores. Doloremque necessitatibus esse illum.
                    Sapiente commodi natus accusantium, maiores nostrum quidem debitis laborum consectetur rerum possimus eius quasi ea odit doloremque reiciendis omnis, sit non illum repellat architecto eos earum libero a. Maxime, veritatis?
                    Sed suscipit autem consectetur, officia eos, quaerat in nemo temporibus dolorum aspernatur quibusdam fuga quis eligendi aliquam ullam nesciunt minus dolor beatae sunt voluptates quo! Perspiciatis officiis enim unde officia.
                    Accusantium quia deleniti id voluptas voluptatibus nemo eaque, iure harum, cumque blanditiis asperiores distinctio hic numquam corrupti! Exercitationem veritatis, consequuntur praesentium rerum ab in labore numquam, repellat aliquid, laboriosam nulla?
                    Incidunt, qui consequuntur placeat numquam in obcaecati ad consectetur nemo adipisci. Maxime repudiandae natus at dolor alias, quo doloribus libero expedita exercitationem illo veniam perferendis. In illum consectetur reprehenderit consequuntur!
                    Quod fugiat, adipisci libero neque consequatur voluptatibus, dolores voluptate odio quia id corporis impedit vel quis totam fuga cupiditate debitis facilis? Facilis impedit veniam quam cum suscipit asperiores officia nihil.
                    Corporis quibusdam cupiditate nobis consequuntur tempore assumenda ducimus eum deleniti explicabo pariatur iusto repellat laborum dolorum, aliquam qui tempora, harum, quaerat praesentium autem nostrum. Tempora praesentium aperiam ex commodi animi.
                    Voluptate animi atque soluta autem velit iusto culpa sint corporis excepturi tenetur tempora consectetur porro aut nemo quibusdam neque, modi ad! Ex recusandae expedita officiis illum nostrum voluptas alias exercitationem?
                    Harum voluptatum alias ad, nihil consequuntur saepe ipsa illo dolorem unde consectetur autem laboriosam. Nostrum, neque nam officiis dignissimos, natus voluptate quia fugiat fuga ipsa numquam iure quae veniam eveniet?
                    Quos dicta laudantium praesentium sed. Repellat magnam ex, veniam doloremque consequatur similique, est sint a impedit amet ut consectetur voluptatum voluptatem laborum facere in excepturi nobis animi nam cupiditate quas.
                    Reiciendis totam atque, dolor maiores laborum, voluptas deserunt amet quasi ipsam voluptate, non eius animi blanditiis dolore! Fugiat rerum necessitatibus quas id nihil, culpa nisi, veniam repellendus, temporibus quisquam quia.
                    Aut necessitatibus eligendi repellat ullam commodi, harum labore similique veniam magnam voluptas magni atque at dolor qui nemo odio quo temporibus id corporis. Culpa expedita fugit deserunt voluptates labore assumenda.
                    Culpa laboriosam praesentium, nulla at repudiandae beatae quibusdam animi molestiae nisi voluptatum voluptates illo illum eveniet adipisci eos officiis veritatis? Esse consequuntur numquam architecto ratione itaque dolor soluta aliquam tenetur.
                    Iste recusandae fuga in tempore dolor similique praesentium mollitia illum labore quaerat obcaecati suscipit voluptates incidunt aliquam, enim veniam, esse eaque quisquam molestias corrupti rerum illo earum atque a! Officia!
                    At exercitationem, impedit deserunt esse eum deleniti ullam a quaerat eius optio quos voluptatibus maiores corrupti provident nihil vitae atque harum est, recusandae aspernatur similique ea? Nesciunt explicabo nisi inventore.
                    Inventore voluptatem obcaecati repellat dolorum officiis commodi facilis quod, perspiciatis ratione mollitia, nulla ipsum dicta consequatur porro totam, possimus itaque sit aliquam accusantium! Et voluptatum voluptatem sapiente officiis inventore voluptas!
                    Ipsam architecto corrupti praesentium ut, a placeat. Corrupti, sint. Atque quo fugiat sint molestiae aperiam fugit dicta quibusdam commodi aliquam error? Fugiat quod ad distinctio excepturi sint voluptas minima at.
                    Vitae debitis sapiente dolorum similique obcaecati eos nemo eum? Nesciunt quam mollitia modi, eaque quas repellat dolore perferendis, quaerat assumenda possimus optio laborum dolor odio repudiandae tempore excepturi quos exercitationem.
                    Suscipit in maiores expedita numquam alias necessitatibus cupiditate eos, laboriosam repellendus dolorum doloremque culpa animi assumenda modi quae quibusdam? Deserunt rerum at velit harum vero laboriosam, aut assumenda facere nam!
                    Vel, fugit in? Dolores ut fugiat voluptatum labore facilis deleniti, ea sapiente autem sed, ipsum, nihil optio rem. Quas, doloremque quaerat. Natus harum molestiae similique quis aut rem ut quia?
                    Quibusdam praesentium unde hic eligendi architecto, officia sapiente delectus accusantium ducimus non perferendis sed aspernatur minus sint quae quos? Hic facere tenetur odio aspernatur autem fuga quidem, sapiente voluptate reprehenderit.</p>
            </div>
        </div>
    </div>
    </div>
</main>

<?php
require_once('footer.php');
?>