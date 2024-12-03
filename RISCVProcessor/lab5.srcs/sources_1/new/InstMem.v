`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 05/15/2024 12:14:33 PM
// Design Name: 
// Module Name: InstMem
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module InstMem(
    input [7:0] addr,
    output [31:0] instruction
    );
    reg [31:0] memory [19:0];
    initial
    begin
        memory[0] = 32'h00007033; // and x0 , x0 , x0         32'h00000000
        memory[1] = 32'h00100093; // addi x1 , x0 , 1         32'h00000001
        memory[2] = 32'h00200113; // addi x2 , x0 , 2         32'h00000002
        memory[3] = 32'h00308193; // addi x3 , x1 , 3         32'h00000004
        memory[4] = 32'h00408213; // addi x4 , x1 , 4         32'h00000005
        memory[5] = 32'h00510293; // addi x5 , x2 , 5         32'h00000007
        memory[6] = 32'h00610313; // addi x6 , x2 , 6         32'h00000008
        memory[7] = 32'h00718393; // addi x7 , x3 , 7         32'h0000000B
        memory[8] = 32'h00208433; // add x8 , x1 , x2         32'h00000003
        memory[9] = 32'h404404b3; // sub x9 , x8 , x4         32'hfffffffe
        memory[10] = 32'h00317533; // and x10 , x2 , x3       32'h00000000
        memory[11] = 32'h0041e5b3; // or x11 , x3 , x4        32'h00000005
        memory[12] = 32'h0041a633; // if x3 is less than x4 , then x12 = 1
                                   //                         32'h00000001
        memory[13] = 32'h007346b3; // nor x13 , x6 , x7       32'hfffffff4
        memory[14] = 32'h4d34f713; // andi x14 , x9 , "4D3"   32'h000004d2
        memory[15] = 32'h8d35e793; // ori x15 , x11 , "8D3"   32'hfffff8d7
        memory[16] = 32'h4d26a813; // if x13 is less than 32'h000004d2 , then x16 = 1
                                   //                         32'h00000000
        memory[17] = 32'h4d244893; // nori x17 , x8 , "4D2"   32'hfffffb2c
        memory[18] = 32'h02b02823;
        memory[19] = 32'h03002603;
    end
    assign instruction = memory[addr[7:2]];
    
endmodule
